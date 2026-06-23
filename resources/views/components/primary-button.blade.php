<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition duration-200 shadow-lg shadow-blue-600/30 tracking-wide text-sm']) }}>
    {{ $slot }}
</button>