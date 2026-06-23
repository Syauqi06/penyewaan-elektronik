<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                Pengaturan Profil & Rekening
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition">&larr; Kembali ke Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="p-8 sm:p-10 bg-white shadow-xl shadow-gray-200/40 sm:rounded-[2rem] border border-gray-100">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-8 sm:p-10 bg-white shadow-xl shadow-gray-200/40 sm:rounded-[2rem] border border-gray-100">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-8 sm:p-10 bg-white shadow-xl shadow-gray-200/40 sm:rounded-[2rem] border border-red-50">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>