{{-- resources/view/quiz/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">

            <form method="POST" action="{{ route('quiz.store', $test->id) }}">
                @csrf

                @foreach ($test->questions as $index => $question)
                    <div class="mb-6 border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-800 mb-2">
                            {{ $index + 1 }}. {{ $question->question_text }}
                        </h3>

                        @foreach ($question->options as $key => $option)
                            <label class="block mb-1">
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $key }}"
                                    class="mr-2 text-indigo-600 focus:ring-indigo-500"
                                    required
                                >
                                {{ strtoupper($key) }}. {{ $option }}
                            </label>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Selesai Tes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
