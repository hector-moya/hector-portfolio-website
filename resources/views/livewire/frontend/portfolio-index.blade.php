<div>
    <x-themes.greenpeace>
        <div class="text-center mb-12 space-y-8">
            <flux:heading class="!text-4xl">{{ __('Portfolio') }}</flux:heading>
            <flux:text class="!text-xl">
                {{ __('Our recent projects and work') }}
            </flux:text>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($projects as $project)
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    @if($projectImage = $project->elements->firstWhere('handle', 'project_image')?->value)
                        <img src="{{ $projectImage }}" alt="{{ $project->title }}" class="w-full h-64 object-cover">
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                            {{ $project->title }}
                        </h2>
                        @if($client = $project->elements->firstWhere('handle', 'client')?->value)
                            <p class="mt-1 text-sm text-indigo-600 dark:text-indigo-400">{{ $client }}</p>
                        @endif
                        @if($description = $project->elements->firstWhere('handle', 'description')?->value)
                            <p class="mt-3 text-zinc-600 dark:text-zinc-400 line-clamp-3">
                                {{ $description }}
                            </p>
                        @endif
                        @if($technologies = $project->elements->firstWhere('handle', 'technologies')?->value)
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Technologies</p>
                                <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ $technologies }}</p>
                            </div>
                        @endif
                        <div class="mt-6 flex gap-3">
                            @if($projectUrl = $project->elements->firstWhere('handle', 'project_url')?->value)
                                <a href="{{ $projectUrl }}" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    View Project →
                                </a>
                            @endif
                            @if($githubUrl = $project->elements->firstWhere('handle', 'github_url')?->value)
                                <a href="{{ $githubUrl }}" target="_blank" class="text-sm text-zinc-600 dark:text-zinc-400 hover:underline">
                                    GitHub ↗
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-zinc-600 dark:text-zinc-400">No portfolio projects available yet.</p>
                </div>
            @endforelse
        </div>
    </x-themes.greenpeace>
</div>
