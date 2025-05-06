<x-layout>
    <div class="mb-8">
        Welcome {{ $user->name }}!
    </div>

    <form action="{{ @route('logout') }}" method="post" style="display: contents">
        <button class="btn btn-secondary" type="submit">Log Out</button>
        @csrf
    </form>

</x-layout>
