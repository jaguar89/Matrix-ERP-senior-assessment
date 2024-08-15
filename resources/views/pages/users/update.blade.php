@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Update User
        </h2>
        <nav class="flex space-x-4">
            <a href="javascript:window.history.back()"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold flex items-center py-2 px-4 rounded">
                <!-- Back Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" fill="currentColor" class="mr-2"
                     viewBox="0 0 448 512">
                    <path
                        d="M257.5 445.1c-4.8 5.1-11.3 7.9-18.1 7.9c-7.5 0-14.9-3.1-20.2-9.2l-192-200c-10.3-10.7-10.3-27.6 0-38.3l192-200c9.1-9.5 23.4-11.6 34.6-5.3c11.2 6.3 15.9 19.8 11.5 31.6L101.2 224H424c13.3 0 24 10.7 24 24s-10.7 24-24 24H101.2l149.7 163.1c8.9 9.7 8.5 24.9-1.1 34z"/>
                </svg>
                <!-- Link Text -->
                Back
            </a>
        </nav>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class=" p-6 rounded-lg ">
                    <form action="{{route('users.update' , $user)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Image Upload -->
                        <div class="mt-4">
                            <x-input-label for="photo" :value="__('Photo')"/>
                            <x-file-input id="photo" class="block mt-1 w-full" type="file"
                                          name="photo"
                                          autofocus autocomplete="photo"/>
                            <x-input-error :messages="$errors->get('photo')" class="mt-2"/>
                        </div>


                        <!-- Username -->
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username')"/>
                            <x-text-input id="username" class="block mt-1 w-full" type="text"
                                          value="{{ old('username', $user->username) }}"
                                          name="username" required autofocus autocomplete="username"/>
                            <x-input-error :messages="$errors->get('username')" class="mt-2"/>
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')"/>
                            <x-text-input id="email" class="block mt-1 w-full" type="email"
                                          name="email" value="{{ old('email', $user->email) }}"
                                          required autocomplete="username"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                        </div>

                        <!-- First Name & Last Name -->
                        <div class="flex flex-row items-center justify-between gap-4">
                            <!-- Prefix Name -->
                            <div class="mt-4 w-1/4">
                                <x-input-label for="prefixname" :value="__('Prefix')"/>
                                <select id="prefixname" name="prefixname" autofocus
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value=""></option>
                                    @foreach (App\Enums\PrefixName::cases() as $key => $value)
                                        <option value="{{ $value }}">
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('prefixname')" class="mt-2"/>
                            </div>
                            <!-- First Name -->
                            <div class="mt-4 w-full">
                                <x-input-label for="firstname" :value="__('First Name')"/>
                                <x-text-input id="firstname" class="block mt-1 w-full"
                                              type="text" name="email" value="{{ old('firstname', $user->firstname) }}"
                                              name="firstname" required autofocus autocomplete="firstname"/>
                                <x-input-error :messages="$errors->get('firstname')" class="mt-2"/>
                            </div>
                            <!-- Middle Name -->
                            <div class="mt-4 w-full">
                                <x-input-label for="middlename" :value="__('Middle Name')"/>
                                <x-text-input id="middlename" class="block mt-1 w-full"
                                              type="text" value="{{ old('middlename', $user->middlename) }}"
                                              name="middlename" required autofocus autocomplete="middlename"/>
                                <x-input-error :messages="$errors->get('middlename')" class="mt-2"/>
                            </div>
                            <!-- Last Name -->
                            <div class="mt-4 w-full">
                                <x-input-label for="lastname" :value="__('Last Name')"/>
                                <x-text-input id="lastname" class="block mt-1 w-full" type="text"
                                              value="{{ old('lastname', $user->lastname) }}"
                                              name="lastname" required autofocus autocomplete="lastname"/>
                                <x-input-error :messages="$errors->get('lastname')" class="mt-2"/>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')"/>

                            <x-text-input id="password" class="block mt-1 w-full"
                                          type="password"
                                          name="password"
                                          autocomplete="new-password"/>

                            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')"/>

                            <x-text-input id="password_confirmation"
                                          class="block mt-1 w-full"
                                          type="password"
                                          name="password_confirmation" autocomplete="new-password"/>

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                        </div>


                        <!-- Submit Button -->
                        <x-primary-button class="mt-4 py-4 px-6">
                            {{ __('Update') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
