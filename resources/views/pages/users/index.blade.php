@extends('layouts.app')

@section('header')
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                All Users
            </h2>
            <nav class="flex space-x-4">
                <a href="{{ route('users.create') }}"
                   class="bg-blue-300 hover:bg-blue-400 text-black font-bold flex items-center gap-3 p-3 rounded"
{{--                   wire:navigate--}}
                   title="Create User">
                    New User
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20" viewBox="0 0 640 512">
                        <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                        <path
                            d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM504 312V248H440c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V136c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H552v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                    </svg>
                </a>
                <a href="{{ route('users.trashed') }}"
                   class="bg-red-300 hover:bg-red-400  text-black font-bold flex items-center gap-3 p-3 rounded"
{{--                   wire:navigate--}}
                   title="Trashed User">
                    Trashed Users
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512">
                        <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                        <path
                            d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                    </svg>
                </a>
            </nav>
        </div>
@endsection

@section('content')
    <div class="container mx-auto p-10">
        @if(session('success'))
            <!-- success Alert -->
            <div x-data="{ alertIsVisible: true }"  x-show="alertIsVisible"
                 class="relative mb-2 w-full overflow-hidden rounded-xl border border-green-600 bg-white text-slate-700 dark:bg-slate-900 dark:text-slate-300"
                 role="alert" x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <div class="flex w-full items-center gap-2 bg-green-600/10 p-4">
                    <div class="bg-green-600/15 text-green-600 rounded-full p-1" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <h3 class="text-sm font-semibold text-green-600">{{ session('title') }}</h3>
                        <p class="text-xs font-medium sm:text-sm">{{ session('message') }}</p>
                    </div>
                    <button type="button" @click="alertIsVisible = false" class="ml-auto" aria-label="dismiss alert">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor"
                             fill="none" stroke-width="2.5" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- table of content -->
        <table class="min-w-full bg-white shadow-lg rounded-lg">
            <thead>
            <tr>
                <th class="py-4 px-4 ">Image</th>
                <th class="py-4 px-4">Username</th>
                <th class="py-4 px-4">Full Name</th>
                <th class="py-4 px-4">Email</th>
                <th class="py-4 px-4">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr class="text-center border border-l-0 border-r-0">
                    <td class="py-4  px-4 ">
                        <img src="{{   $user->avatar }}" alt="User Photo"
                             class="w-10 h-auto  object-cover rounded">
{{--                        @if($user->photo)--}}
{{--                            <a href="{{ asset('storage/' . $user->photo) }}" target="_blank">--}}
{{--                                <img src="{{ asset('storage/' . $user->photo) }}" alt=""--}}
{{--                                     class="w-10 h-auto mx-auto object-cover rounded">--}}
{{--                            </a>--}}
{{--                        @else--}}
{{--                            <svg class="w-10 h-auto mx-auto" xmlns="http://www.w3.org/2000/svg" height="16" width="16"--}}
{{--                                 viewBox="0 0 512 512">--}}
{{--                                <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->--}}
{{--                                <path--}}
{{--                                    d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>--}}
{{--                            </svg>--}}
{{--                        @endif--}}
                    </td>
                    <td class="py-2 px-4 ">{{ $user->username }}</td>
                    <td class="py-2 px-4 ">{{  $user->fullName }}</td>
                    <td class="py-2 px-4 ">{{  $user->email }}</td>
                    <td>
                        <div class="w-full h-full flex justify-center items-center space-x-4 ">
                            <!-- Show User Link -->
                            <a href="{{ route('users.show', $user->id) }}" target="_blank"
                               class="bg-green-200 hover:bg-green-400 text-white font-bold px-2 py-2 rounded"
                               title="View User">
                                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 576 512">
                                    <!-- Font Awesome Free 6.5.1 - Eye Icon -->
                                    <path
                                        d="M572.52 241.4c-54.47-106.7-163.9-177.4-284.5-177.4S57.95 134.7 3.482 241.4a48.006 48.006 0 0 0 0 29.2c54.47 106.7 163.9 177.4 284.5 177.4s230-70.75 284.5-177.4a48.006 48.006 0 0 0 0-29.2zM288 400c-61.86 0-112-50.14-112-112s50.14-112 112-112 112 50.14 112 112-50.1 112-112 112zm0-160c-26.47 0-48 21.53-48 48s21.53 48 48 48 48-21.53 48-48-21.5-48-48-48z"/>
                                </svg>
                            </a>

                            <!-- Update User Link -->
                            <a href="{{route('users.edit' , $user->id)}}"
                               class="bg-blue-200 hover:bg-blue-400 text-white font-bold px-2 py-2 rounded"
                               title="Update User">
                                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512">
                                    <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                </svg>
                            </a>

                            <!-- Delete User Modal -->
                            <div x-data="{ warningModalIsOpen: false }">
                                <button @click="warningModalIsOpen = true" type="button"
                                        class="bg-amber-300 hover:bg-amber-400 text-white font-bold p-2 rounded"
                                        title="Delete User">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14"
                                         viewBox="0 0 448 512">
                                        <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                        <path
                                            d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                    </svg>
                                </button>
                                <div x-cloak x-show="warningModalIsOpen" x-transition.opacity.duration.200ms
                                     x-trap.inert.noscroll="warningModalIsOpen"
                                     @keydown.esc.window="warningModalIsOpen = false"
                                     @click.self="warningModalIsOpen = false"
                                     class="fixed inset-0 z-30 flex items-end justify-center bg-black/20 p-4 pb-8 backdrop-blur-md sm:items-center lg:p-8"
                                     role="dialog" aria-modal="true" aria-labelledby="warningModalTitle">
                                    <!-- Modal Dialog -->
                                    <div x-show="warningModalIsOpen"
                                         x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                                         x-transition:enter-start="opacity-0 scale-50"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-xl border border-slate-300 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        <!-- Dialog Header -->
                                        <div
                                            class="flex items-center justify-between border-b border-slate-300 bg-slate-100/60 px-4 py-2 dark:border-slate-700 dark:bg-slate-900/20">
                                            <div
                                                class="flex items-center justify-center rounded-full bg-amber-500/20 text-amber-500 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                     fill="currentColor" class="size-6" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <button @click="warningModalIsOpen = false" aria-label="close modal">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                     aria-hidden="true" stroke="currentColor" fill="none"
                                                     stroke-width="1.4" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- Dialog Body -->
                                        <div class="px-4 text-center">
                                            <h3 id="warningModalTitle"
                                                class="mb-2 font-semibold tracking-wide text-black dark:text-white">
                                                Confirm delete</h3>
                                            <p>Are you sure you want to delete user: {{ $user->firstname }}?</p>
                                        </div>
                                        <!-- Dialog Footer -->
                                        <div
                                            class="flex items-center justify-center border-slate-300 p-4 dark:border-slate-700">
                                            <form action="{{route('users.destroy' ,  $user->id)}}" method="POST" class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    @click="warningModalIsOpen = false;"
                                                    type="submit"
                                                    class="w-full cursor-pointer whitespace-nowrap rounded-xl bg-amber-500 px-4 py-2 text-center text-sm font-semibold tracking-wide text-white transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 active:opacity-100 active:outline-offset-0">
                                                    Soft delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
