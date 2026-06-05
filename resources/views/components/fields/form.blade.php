@props(['section', 'assets' => collect()])

@php
    $title = $section['data']['title'] ?? '';
@endphp

{{-- The page-builder "form" section renders the functional contact form
     (wired to ContactForm + SubmitContactForm with validation, honeypot,
     and rate limiting). --}}
<livewire:frontend.contact-form :title="$title" :key="'contact-'.($section['_id'] ?? 'form')" />
