<x-layout>
    <div class="flex justify-center mb-24">

        <form class="w-[600px] min-w-[400px] bg-base-200 p-6 mt-[200px] rounded-box"
              enctype="multipart/form-data" action="{{ route('register.seller.submit') }}"
              method="POST"
        >
            <div class="text-base-content/80 text-3xl mb-16">Business Registration</div>
            <!--div class="flex justify-center mb-16">
                <ul class="timeline">
                    <li>
                        <div class="timeline-middle">
                            <x-checkmark class="text-primary" />
                        </div>
                        <div class="timeline-end timeline-box">Business Information</div>
                        <hr />
                    </li>
                    <li>
                        <hr />
                        <div class="timeline-middle">
                            <x-checkmark class="text-base-content/40" />
                        </div>
                        <div class="timeline-end timeline-box text-base-content/40">Owner Information</div>
                        <hr />
                    </li>
                    <li>
                        <hr />
                        <div class="timeline-middle">
                            <x-checkmark class="text-base-content/40" />
                        </div>
                        <div class="timeline-end timeline-box text-base-content/40">Store Settings</div>
                    </li>
                </ul>
            </div-->

            <x-form.input id="business_name" label="Business Name:" class="mb-8" />
            <x-form.input id="business_description" label="Business Description:" class="mb-8" :textarea="true" />
            <x-form.input id="business_address" label="Business Address:" class="mb-8" :textarea="true"/>
            <x-form.input id="business_phone" label="Phone Number:" class="mb-8" />
            <x-form.input id="business_email" label="Business Email:" class="mb-8" />
            <x-form.input id="logo" label="Logo:" class="mb-10" type="slot">
                <x-form.saner-upload-input id="logo" />
            </x-form.input>

            <x-form.category-title class="mb-8">Operating Hour</x-form.category-title>

            <x-form.input id="opening_hour" label="Opening Hour:" class="mb-8" />
            <x-form.input id="closing_hour" label="Closing Hour:" class="mb-10" />

            <x-form.category-title class="mb-8">Social Media</x-form.category-title>

            <x-form.input id="facebook" label="Facebook:" class="mb-8" :required="false"/>
            <x-form.input id="instagram" label="Instagram:" class="mb-8" :required="false"/>

            <div class="divider"></div>

            <x-form.category-title class="mb-8">Owner Information</x-form.category-title>
            <x-form.input id="name" label="Name:" class="mb-8" />
            <x-form.input id="email" label="Email:" class="mb-8" />
            <x-form.input id="password" label="Password:" class="mb-8" type="password"/>
            <x-form.input id="password_confirmation" label="Password Confirmation:" class="mb-10" type="password"/>

            <x-form.category-title class="mb-8">Identification</x-form.category-title>
            <x-form.input id="ic_number" label="IC:" class="mb-8" />
            <x-form.input id="business_cert" label="Business Certificate:" class="mb-8" type="slot">
                <x-form.saner-upload-input id="business_cert" accept=".pdf"/>
            </x-form.input>

            <div class="divider"></div>

            <x-form.category-title class="mb-8">Payment Details</x-form.category-title>
            <x-form.input id="bank_name" label="Bank:" class="mb-8" type="slot">
                <select class="select" id="bank_name" name="bank_name">
                    <option disabled selected>Choose a bank</option>
                    <option>Maybank</option>
                    <option>Bank Islam</option>
                </select>
            </x-form.input>
            <x-form.input id="bank_account_name" label="Bank Account Name:" class="mb-8" />
            <x-form.input id="bank_account_number" label="Bank Account Number:" class="mb-24" />


            <div class="flex">
                <div class="basis-[200px]"></div>
                <div class="grow flex gap-8 justify-items-start">
                    <button class="btn btn-primary" type="submit">Submit</button>
                    <button class="btn btn-outline btn-secondary"
                       id="back_1" type="reset">Reset</button>
                    <a class="btn btn-ghost btn-secondary text-secondary hover:text-secondary-content"
                        href="{{ route('register') }}">Back</a>
                </div>
            </div>
            @csrf
        </form>
    </div>
</x-layout>
