<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Amanda Cojerean') }} &middot; Portfolio</title>
        <meta name="description" content="Amanda Cojerean — software engineer. Projects from UCI Coding Bootcamp and beyond.">

        <link rel="icon" href="/favicon.ico?v=2" sizes="any">
        <link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">

        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-paper font-sans text-ink antialiased">
        <a href="#projects" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:rounded-full focus:bg-ink focus:px-4 focus:py-2 focus:text-paper">
            Skip to projects
        </a>

        <header
            x-data="{ mobileMenuOpen: false }"
            @keydown.escape.window="mobileMenuOpen = false"
            class="sticky top-0 z-40 border-b-2 border-pink-200 bg-paper-card/95 shadow-sm shadow-ink/5 backdrop-blur"
        >
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-3 px-4 py-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 font-serif text-base font-medium tracking-tight whitespace-nowrap text-ink sm:text-lg">
                    <span class="size-2 rounded-full bg-pink-500"></span>
                    Amanda Cojerean
                </a>

                <nav class="hidden items-center gap-5 text-sm text-ink-soft sm:flex">
                    <a href="#projects" class="hover:text-pink-600">Projects</a>
                    <a href="#about" class="hover:text-pink-600">About</a>
                    <a href="#contact" class="hover:text-pink-600">Contact</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-pink-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-pink-600">Log in</a>
                    @endauth
                </nav>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="flex size-9 items-center justify-center rounded-full border border-mustard-300 text-ink transition hover:border-teal-500 sm:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Toggle menu"
                >
                    <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="size-5">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="size-5" style="display: none;">
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <nav
                x-show="mobileMenuOpen"
                x-cloak
                x-transition
                @click="mobileMenuOpen = false"
                class="flex flex-col gap-1 border-t border-pink-100 bg-paper-card px-4 py-3 text-sm text-ink-soft sm:hidden"
            >
                <a href="#projects" class="rounded-lg px-2 py-2 hover:bg-pink-50 hover:text-pink-600">Projects</a>
                <a href="#about" class="rounded-lg px-2 py-2 hover:bg-pink-50 hover:text-pink-600">About</a>
                <a href="#contact" class="rounded-lg px-2 py-2 hover:bg-pink-50 hover:text-pink-600">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-2 py-2 hover:bg-pink-50 hover:text-pink-600">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-2 py-2 hover:bg-pink-50 hover:text-pink-600">Log in</a>
                @endauth
            </nav>
        </header>

        <main>
            {{-- Hero --}}
            <section class="mx-auto max-w-5xl px-4 pt-12 pb-14 sm:px-6 sm:pt-20 sm:pb-20">
                <div class="rounded-3xl border border-mustard-100 bg-paper-card p-6 shadow-lg shadow-ink/5 sm:p-12">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-pink-600">Software Engineer</p>
                    <h1 class="mt-4 max-w-3xl font-serif text-4xl leading-tight font-medium text-ink sm:text-6xl">
                        I build clean, considered software&mdash;
                        <span class="italic text-teal-600">and I like getting the details right.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg text-ink-soft">
                        From UCI's Coding Bootcamp to my work today, here's a look at what I've built along the way.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a
                            href="#projects"
                            class="inline-flex items-center gap-2 rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:bg-pink-600"
                        >
                            See my work
                        </a>

                        <div class="flex items-center gap-3">
                            <a
                                href="https://github.com/acerjak"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="GitHub"
                                class="flex size-11 items-center justify-center rounded-full border border-mustard-300 text-ink-soft transition hover:border-teal-500 hover:text-pink-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.833.092-.647.35-1.088.636-1.339-2.221-.253-4.556-1.113-4.556-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.269 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.295 2.747-1.026 2.747-1.026.546 1.378.203 2.397.1 2.65.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.679.921.679 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                                </svg>
                            </a>
                            <a
                                href="https://gitlab.com/acerjak"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="GitLab"
                                class="flex size-11 items-center justify-center rounded-full border border-mustard-300 text-ink-soft transition hover:border-teal-500 hover:text-pink-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path d="M22.65 14.39L12 22.13 1.35 14.39a.84.84 0 0 1-.3-.94l1.22-3.78 2.44-7.51A.42.42 0 0 1 4.82 2a.43.43 0 0 1 .58 0 .42.42 0 0 1 .11.18l2.44 7.49h8.1l2.44-7.51A.42.42 0 0 1 18.6 2a.43.43 0 0 1 .58 0 .42.42 0 0 1 .11.18l2.44 7.51L23 13.45a.84.84 0 0 1-.35.94z" />
                                </svg>
                            </a>
                            <a
                                href="https://www.linkedin.com/in/acerjak/"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="LinkedIn"
                                class="flex size-11 items-center justify-center rounded-full border border-mustard-300 text-ink-soft transition hover:border-teal-500 hover:text-pink-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </a>
                            <a
                                href="https://www.instagram.com/yourcraftyboo/"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Instagram"
                                class="flex size-11 items-center justify-center rounded-full border border-mustard-300 text-ink-soft transition hover:border-teal-500 hover:text-pink-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.332.014 7.052.072 2.694.272.273 2.69.073 7.052.014 8.332 0 8.741 0 12s.014 3.668.072 4.948c.2 4.358 2.618 6.78 6.98 6.98C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z" />
                                </svg>
                            </a>
                            <a
                                href="https://www.tiktok.com/@amandacojerean"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="TikTok"
                                class="flex size-11 items-center justify-center rounded-full border border-mustard-300 text-ink-soft transition hover:border-teal-500 hover:text-pink-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path d="M16.6 5.82c-.7-.77-1.13-1.76-1.22-2.82h-3.1v13.61c0 1.6-1.3 2.9-2.9 2.9a2.9 2.9 0 01-2.9-2.9 2.9 2.9 0 012.9-2.9c.27 0 .53.04.78.11V10.7a6.09 6.09 0 00-.78-.05A6 6 0 003.4 16.6a6 6 0 006 6 6 6 0 006-6V9.03a8.16 8.16 0 004.77 1.53V7.46a4.85 4.85 0 01-3.57-1.64z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Behind the code --}}
            <section class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 sm:pb-20">
                <div class="flex flex-col items-center gap-6 rounded-3xl border border-pink-100 bg-paper-card p-8 text-center shadow-md shadow-ink/5 sm:p-12">
                    <img
                        src="{{ asset('images/amanda.jpg') }}"
                        alt="Portrait of Amanda Cojerean"
                        class="h-30 w-24 rounded-full border-4 border-mustard-300 object-cover shadow-sm sm:h-36 sm:w-30"
                    >
                    <div>
                        <p class="font-serif text-2xl font-medium text-ink">Hi, I'm Amanda</p>
                        <p class="mt-3 max-w-xl text-ink-soft">
                            Thanks for stopping by! Whether you're here about a role, a project, or just curious what
                            I've built, I'd love to hear from you.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Projects --}}
            <section id="projects" class="border-t border-pink-100/60 bg-paper-muted">
                <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-20">
                    <h2 class="font-serif text-3xl font-medium text-ink">Projects</h2>
                    <p class="mt-2 max-w-xl text-ink-soft">
                        A mix of team builds from UCI's Coding Bootcamp and work I've done on my own.
                    </p>

                    <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2">
                        @forelse ($projects as $project)
                            <article class="group flex flex-col overflow-hidden rounded-2xl border border-pink-100 bg-paper-card shadow-md shadow-ink/5 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-ink/10">
                                <div class="aspect-video w-full overflow-hidden bg-mustard-50">
                                    @if ($project->image_path)
                                        <img
                                            src="{{ asset($project->image_path) }}"
                                            alt="{{ $project->title }} preview"
                                            loading="lazy"
                                            class="h-full w-full object-cover object-top"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <span class="font-serif text-2xl italic text-mustard-500">{{ $project->title }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col gap-3 p-6">
                                    <div>
                                        <h3 class="font-serif text-xl font-medium text-ink">{{ $project->title }}</h3>
                                        @if ($project->role)
                                            <p class="text-xs font-medium tracking-wide text-teal-600 uppercase">{{ $project->role }}</p>
                                        @endif
                                    </div>

                                    <p class="text-sm text-ink-soft">{{ $project->tagline ?? $project->description }}</p>

                                    @if ($project->tech_stack)
                                        <ul class="mt-1 flex flex-wrap gap-2">
                                            @foreach ($project->tech_stack as $tech)
                                                <li class="rounded-full bg-pink-50 px-2.5 py-1 text-xs font-medium text-pink-700">
                                                    {{ $tech }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <div class="mt-auto flex items-center gap-4 pt-3 text-sm font-medium">
                                        @if ($project->demo_url)
                                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="text-pink-600 hover:text-pink-700">
                                                Live demo &rarr;
                                            </a>
                                        @endif
                                        @if ($project->repo_url)
                                            <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="text-ink-soft hover:text-teal-600">
                                                View code &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-ink-soft">Projects coming soon.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- About --}}
            <section id="about" class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-20">
                <div class="rounded-3xl border border-teal-100 bg-paper-card p-6 shadow-md shadow-ink/5 sm:p-10">
                    <h2 class="font-serif text-3xl font-medium text-ink">About</h2>
                    <p class="mt-4 max-w-2xl text-ink-soft">
                        I'm a software engineer based in Orange County, CA. I got my start through UCI's Coding
                        Bootcamp and have been building ever since&mdash;currently at Neon Bang.
                    </p>
                </div>
            </section>

            {{-- Contact --}}
            <section id="contact" class="border-t border-pink-100/60 bg-paper-muted">
                <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-20">
                    <div class="rounded-3xl border border-mustard-100 bg-paper-card p-6 shadow-md shadow-ink/5 sm:p-10">
                        <h2 class="font-serif text-3xl font-medium text-ink">Get in touch</h2>
                        <p class="mt-2 max-w-xl text-ink-soft">
                            Have a project, a role, or just want to say hi? Send me a message below.
                        </p>

                        <div class="mt-8">
                            <livewire:pages::inquiry-form />
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-mustard-100">
            <div class="mx-auto flex max-w-5xl flex-col items-start justify-between gap-4 px-4 py-10 sm:flex-row sm:items-center sm:px-6">
                <p class="text-sm text-mustard-700">&copy; {{ now()->year }} Amanda Cojerean. Built with Laravel.</p>
                <a href="#contact" class="text-sm font-medium text-pink-700 hover:text-pink-600">
                    Get in touch &rarr;
                </a>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
