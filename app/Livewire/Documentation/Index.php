<?php

namespace App\Livewire\Documentation;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Url(as: 'topic')]
    public string $slug = 'activity-log';

    /**
     * @return array<int, array{slug: string, title: string, icon: string}>
     */
    public function topics(): array
    {
        return [
            ['slug' => 'activity-log', 'title' => 'Activity Log', 'icon' => 'clock'],
            ['slug' => 'form-submissions', 'title' => 'Form Submissions', 'icon' => 'envelope'],
            ['slug' => 'users', 'title' => 'Users', 'icon' => 'user-group'],
        ];
    }

    /**
     * @return array{slug: string, title: string, icon: string}
     */
    public function currentTopic(): array
    {
        $topics = $this->topics();

        foreach ($topics as $topic) {
            if ($topic['slug'] === $this->slug) {
                return $topic;
            }
        }

        return $topics[0];
    }

    #[Title('Documentation')]
    public function render(): View|Factory
    {
        return view('livewire.documentation.index', [
            'topics' => $this->topics(),
            'currentTopic' => $this->currentTopic(),
        ]);
    }
}
