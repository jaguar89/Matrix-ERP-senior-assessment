@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
{{--            {{ __($title) }}--}}
        </h2>
    </div>
@endsection

@section('content')
<div class="container mx-auto p-10">
    <div class="max-w-4xl mx-auto py-12">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $user->fullName }}
                </h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Photo
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <img src="{{   $user->avatar }}" alt="User Photo"
                                 class="w-20 h-auto  object-cover rounded">
{{--                            @if($user->photo)--}}
{{--                                <a href="{{ asset('storage/' . $user->photo) }}" target="_blank">--}}
{{--                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="User Photo"--}}
{{--                                         class="w-10 h-auto mx-auto object-cover rounded">--}}
{{--                                </a>--}}
{{--                            @else--}}
{{--                                <svg class="w-10 h-auto" xmlns="http://www.w3.org/2000/svg" height="16" width="16"--}}
{{--                                     viewBox="0 0 512 512">--}}
{{--                                    <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->--}}
{{--                                    <path--}}
{{--                                        d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/>--}}
{{--                                </svg>--}}
{{--                            @endif--}}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Name
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $user->prefixname }} {{ $user->firstname }} {{ $user->middlename }} {{ $user->lastname }} {{ $user->suffixname }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Username
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $user->username }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Email
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $user->email }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Account Type
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ ucfirst($user->type) }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Email Verified At
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $user->email_verified_at ? $user->email_verified_at->format('F j, Y, g:i a') : 'Not Verified' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
