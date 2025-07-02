<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EureVerse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ currentTab: 'home' }" class="text-white bg-cover bg-center" style="background-image: url('/img/bg.jpg')">

    <!-- Header -->
    <header class="fixed top-0 left-0 w-full flex justify-between items-center px-6 py-4 bg-gray-800 bg-opacity-80 z-50 backdrop-blur-md">
        <h1 class="text-2xl font-bold">EUREVERSE</h1>
        <a href="{{ route('login') }}" class="bg-white text-gray-800 px-4 py-2 rounded hover:bg-gray-200 transition">
            Log in
        </a>
    </header>

    <!-- Sidebar -->
    <aside class="fixed top-16 left-0 w-1/6 h-[calc(100vh-4rem)] bg-gray-800 bg-opacity-40 border-r border-blue-500 z-40 backdrop-blur-md flex flex-col justify-center items-center space-y-6">
        <button @click="currentTab = 'home'" class="hover:text-blue-400 text-lg">Home</button>
        <button @click="currentTab = 'about'" class="hover:text-blue-400 text-lg">About</button>
        <button @click="currentTab = 'news'" class="hover:text-blue-400 text-lg">News</button>
    </aside>

    <!-- Main Content -->
    <div class="ml-[16.6%] pt-24 px-16 h-[calc(100vh-4rem)] overflow-y-auto">
        <main class="pb-20 space-y-6">

            <!-- Home Tab -->
            <div x-show="currentTab === 'home'" x-transition>
                <h2 class="text-3xl font-semibold mb-4">Selamat Datang di EureVerse!</h2>
                <p class="text-lg mb-2">Tempat belajar interaktif yang seru, kolaboratif, dan penuh peluang.</p>
                <p class="text-lg mb-6">Belajar lebih seru, terhubung dengan sesama, dan raih hasil nyata dari setiap aktivitasmu.</p>

                <a href="{{ route('home') }}" class="bg-white text-black px-5 py-3 rounded shadow hover:bg-gray-300 transition">
                    Get Started >
                </a>

                <!-- 3 Feature Boxes -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                    <div class="bg-white bg-opacity-10 p-6 rounded text-center">
                        <h3 class="text-xl font-semibold mb-2">📚 Modul Interaktif</h3>
                        <p>Pelajari materi dengan cara yang menyenangkan dan aktif.</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded text-center">
                        <h3 class="text-xl font-semibold mb-2">🤝 Kolaborasi Tim</h3>
                        <p>Bergabung dalam tim untuk mengerjakan proyek bareng.</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded text-center">
                        <h3 class="text-xl font-semibold mb-2">🎯 Belajar dengan santai</h3>
                        <p>Bersantai dan belajar dimanapun dan kapanpun kamu mau.</p>
                    </div>
                </div>

                <!-- Swiper Image Carousel -->
                <div 
                    class="relative mt-10 w-full h-64 rounded overflow-hidden bg-white bg-opacity-10" 
                    x-data="{
                        images: ['/img/slide1.jpg','/img/slide2.jpg','/img/slide3.jpg'],
                        currentIndex: 0,
                        interval: null,
                        startAutoSlide() {
                            this.interval = setInterval(() => {
                                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                            }, 3000);
                        },
                        prev() {
                            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                        },
                        next() {
                            this.currentIndex = (this.currentIndex + 1) % this.images.length;
                        },
                        init() {
                            this.startAutoSlide();
                        }
                    }" x-init="init"
                >
                    <template x-for="(image, index) in images" :key="index">
                        <div x-show="index === currentIndex" class="absolute inset-0 transition-opacity duration-700">
                            <img :src="image" alt="" class="w-full h-full object-cover">
                        </div>
                    </template>

                    <!-- Arrows -->
                    <button @click="prev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-30 p-2 rounded-full hover:bg-opacity-50">←</button>
                    <button @click="next" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-30 p-2 rounded-full hover:bg-opacity-50">→</button>
                </div>
            </div>

            <!-- About Tab -->
            <div x-show="currentTab === 'about'" x-transition>
                <h2 class="text-3xl font-semibold mb-4">Tentang EureVerse</h2>
                <p class="text-lg leading-relaxed max-w-3xl">
                    EureVerse adalah platform e-learning modern yang dirancang untuk mempertemukan pembelajar dan kolaborator.
                    Misi kami adalah menciptakan pengalaman belajar yang bermakna, sosial, dan memberi dampak nyata.
                </p>
            </div>

            <!-- News Tab -->
            <div x-show="currentTab === 'news'" x-transition>
                <h2 class="text-3xl font-semibold mb-4">Berita & Pembaruan</h2>
                <ul class="list-disc list-inside text-lg space-y-2">
                    <li>🚀 Fitur baru: Status Online atau Offline teman</li>
                    <li>🚀 Update: Fix Beberapa Bug</li>
                    <li>📢 Update: memperbaiki Visual pada website</li>
                </ul>
            </div>

        </main>
    </div>

</body>
</html>
