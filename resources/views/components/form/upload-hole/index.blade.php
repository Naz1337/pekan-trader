@props([
    'id',
    'name' => null,
])

@use(Illuminate\Support\Js)

<div class="flex items-center gap-4"
    x-data="{
    fileId: $id('file'),
    errors: false,
    imgSrc: ''
}">
    <img src="" alt="" x-cloak x-show="imgSrc !== ''"
        x-bind:src="imgSrc"
        class="w-16 h-16 rounded-box object-cover">

    <button class="btn btn-soft btn-secondary flex items-center gap-4"
        @@click.prevent="
        //console.log('hello world, time to select a file!');
        $refs.fileInput.click();"
        x-ref="fileButton"
    >Choose a file</button>

    <div x-cloak x-show="errors" class="text-error">
        Can't upload file more than 5MB
    </div>

    <input type="file" x-bind:id="fileId" x-ref="fileInput" class="hidden"
        accept="image/*" @@change="
            const file = $event.target.files[0];

            console.log(file);

            const url = new URL({{ Js::from(route('api.uploadthing.presigned-url.logo')) }});
            const fileData = {
                filename: file.name,
                filesize: file.size
            };

            const searchParams = new URLSearchParams(fileData);

            url.search = searchParams.toString();
            const finalLink = url.toString()

            console.log(finalLink);

            $refs.fileButton.innerHTML = `<span class='loading loading-spinner'></span>`;
            $refs.fileButton.disabled = true;
            $refs.fileButton.classList.add('btn-disabled');

            const resp = await fetch(finalLink, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })

            const data = await resp.json();
            console.log(data);

            if (data.errors) {
                $refs.fileButton.innerHTML = 'Choose a file';
                $refs.fileButton.disabled = false;
                $refs.fileButton.classList.remove('btn-disabled');
                errors = true;
                return;
            }

            const uploadUrl = data.uploadLink;
            console.log('here is it ', uploadUrl);

            const formData = new FormData();
            formData.append('file', file);

            console.log('Sending data to UT!');
            const uploadResp = await fetch(uploadUrl, {
                method: 'PUT',
                body: formData
            });

            const uploadData = await uploadResp.json();
            console.log(uploadData);

            $refs.placeUrlHere.value = uploadData.ufsUrl;
            imgSrc = uploadData.ufsUrl;

            setTimeout(() => {
                $refs.fileButton.disabled = false;
                $refs.fileButton.classList.remove('btn-disabled');
                $refs.fileButton.innerHTML = 'Replace this file';
            }, 20)

        ">

    <input type="hidden"
           id="{{ $id }}" name="{{ $name ?? $id }}" x-ref="placeUrlHere">
</div>

