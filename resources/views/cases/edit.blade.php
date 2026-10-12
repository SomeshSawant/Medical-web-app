<x-appLayout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Medical Case: ') }} {{ $case->case_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Update procedure parameters, surgeon, or lifecycle stage
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('cases.show', $case) }}" class="inline-flex items-center px-3.5 py-1.5 border border-gray-300 rounded-md text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                    &larr; View Details
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('cases.update', $case) }}">
                @csrf
                @method('PUT')

                @include('cases._form', ['case' => $case, 'holidays' => $holidays])

                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a href="{{ route('cases.show', $case) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 focus:bg-indigo-700 shadow-sm transition">
                        Update Case
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-appLayout>
