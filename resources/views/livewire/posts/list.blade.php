<?php

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    
    public Collection $posts;
    public ?Post $editing = null;

    public function mount(): void {
        $this->getPosts();
    }

    #[On('post-created')]
    public function getPosts(): void {
        $this->posts = Post::with('user')->latest()->get();
    }


    public function edit(Post $post) : void{
        $this->editing = $post;
        $this->getPosts();
    }

    #[On('post-edit-canceled')]
    #[On('post-updated')] 
    public function disableEditing(): void
    {
        $this->editing = null;
        $this->getPosts();
    } 

    public function delete(Post $post): void {
        $this->authorize('delete', $post);
        $post->delete();
        $this->getPosts();
    }
}; ?>

<div class="mt-6 bg-white shadow-sm rounded-lg divide-y"> 
    @foreach ($posts as $post)
        <div class="p-6 flex space-x-2" wire:key="{{ $post->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-800">{{ $post->user->name }}</span>
                        <small class="ml-2 text-sm text-gray-600">{{ $post->created_at->format('j M Y, g:i a') }}</small>
                        @unless ($post->created_at->eq($post->updated_at))
                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                        @endunless
                    </div>
                    @if ($post->user->is(auth()->user()))
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link wire:click="edit({{ $post->id }})">
                                    {{ __('Edit') }}
                                </x-dropdown-link>
                                <x-dropdown-link wire:click="delete({{ $post->id }})">
                                    {{ __('Delete') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endif
                </div>
                @if ($post->is($editing)) 
                    <livewire:posts.edit :post="$post" :key="$post->id" />
                @else
                    <p class="mt-4 text-lg text-gray-900">{{ $post->message }}</p>
                @endif 
            </div>
        </div>
    @endforeach 
</div>
