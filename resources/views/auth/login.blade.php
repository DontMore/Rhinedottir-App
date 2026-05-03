<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - Rhinedottir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        :root {
            /* Day Theme (matches reference image) */
            --sky: linear-gradient(to bottom, #b2d8f2 0%, #8ebae0 100%);
            --m1: #8ba4c1;
            --m2: #7492b4;
            --m3: #5a7fa8;
            --m4: #3a6186;
            --m5: #1b2a47;
            --cloud: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-color: #0f172a;
            --text-muted: #334155;
            --input-bg: rgba(255, 255, 255, 0.7);
            --input-border: rgba(255, 255, 255, 0.8);
            --input-focus: rgba(255, 255, 255, 0.95);
        }

        body.theme-rain {
            --sky: linear-gradient(to bottom, #4a5a6a 0%, #2c3e50 100%);
            --m1: #5b6c7d;
            --m2: #4c5b6b;
            --m3: #3d4b5a;
            --m4: #2e3a48;
            --m5: #1a2430;
            --cloud: #7b8c9d;
            --glass-bg: rgba(15, 23, 42, 0.5);
            --glass-border: rgba(255, 255, 255, 0.15);
            --text-color: #f8fafc;
            --text-muted: #cbd5e1;
            --input-bg: rgba(30, 41, 59, 0.6);
            --input-border: rgba(255, 255, 255, 0.2);
            --input-focus: rgba(30, 41, 59, 0.9);
        }

        body.theme-night {
            --sky: linear-gradient(to bottom, #0f172a 0%, #020617 100%);
            --m1: #1e293b;
            --m2: #152238;
            --m3: #0f172a;
            --m4: #0a0f1d;
            --m5: #04070d;
            --cloud: rgba(255, 255, 255, 0.05);
            --glass-bg: rgba(2, 6, 23, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(15, 23, 42, 0.6);
            --input-border: rgba(255, 255, 255, 0.15);
            --input-focus: rgba(15, 23, 42, 0.95);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            overflow: hidden; 
            background: var(--sky);
            min-height: 100vh;
        }

        /* Landscape */
        .landscape-container {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            pointer-events: none;
        }

        .mountain-path {
            transition: fill 2s ease;
        }

        /* Celestial Bodies */
        .celestial-body {
            position: absolute;
            border-radius: 50%;
            transition: all 2s ease;
        }
        .sun {
            width: 120px; height: 120px;
            background: #ffde00;
            top: 15%; right: 15%;
            box-shadow: 0 0 60px #ffde00, 0 0 100px #ffde00;
        }
        .moon {
            width: 90px; height: 90px;
            top: 15%; right: 15%;
            border-radius: 50%;
            box-shadow: inset -15px -15px 0 0 #f4f6f0, 0 0 40px rgba(255,255,255,0.3);
            background: transparent;
        }

        /* Weather Effects */
        .weather-layer {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle infinite ease-in-out;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.1; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        .cloud-wrapper {
            position: absolute;
            animation: float linear infinite;
        }
        .cloud {
            position: relative;
            background: var(--cloud);
            width: 120px; height: 40px;
            border-radius: 40px;
            transition: background 2s ease;
        }
        .cloud::before, .cloud::after {
            content: '';
            position: absolute;
            background: var(--cloud);
            border-radius: 50%;
            transition: background 2s ease;
        }
        .cloud::before { width: 60px; height: 60px; top: -30px; left: 15px; }
        .cloud::after { width: 80px; height: 80px; top: -40px; right: 20px; }
        
        @keyframes float {
            0% { transform: translateX(-300px); }
            100% { transform: translateX(110vw); }
        }

        .drop {
            position: absolute;
            background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0.6));
            width: 2px;
            height: 100px;
            animation: rain linear infinite;
        }
        @keyframes rain {
            0% { transform: translateY(-100px) rotate(10deg); }
            100% { transform: translateY(100vh) rotate(10deg); }
        }

        /* Form UI */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .dynamic-text { color: var(--text-color); }
        .dynamic-text-muted { color: var(--text-muted); }

        .glass-input {
            background: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important;
            color: var(--text-color) !important;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: var(--input-focus) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4) !important;
            border-color: rgba(59, 130, 246, 0.6) !important;
        }
        .glass-input::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .bottom-links a {
            transition: color 0.2s, text-shadow 0.2s;
        }
        .bottom-links a:hover {
            color: #60a5fa;
            text-shadow: 0 0 8px rgba(96, 165, 250, 0.5);
        }
    </style>
</head>
<body class="theme-day"> 

    <!-- Inline SVG definitions -->
    <svg width="0" height="0" class="hidden">
      <defs>
        <!-- Pine Tree Pattern -->
        <pattern id="tree-pattern" x="0" y="0" width="120" height="150" patternUnits="userSpaceOnUse" patternTransform="scale(0.7)">
          <polygon points="60,10 80,50 70,50 90,90 30,90 50,50 40,50" fill="var(--m5)" class="mountain-path" />
          <polygon points="20,40 35,70 25,70 45,110 -5,110 15,70 5,70" fill="var(--m5)" class="mountain-path" />
          <polygon points="100,30 115,60 105,60 125,100 75,100 95,60 85,60" fill="var(--m5)" class="mountain-path" />
          <rect x="0" y="90" width="120" height="60" fill="var(--m5)" class="mountain-path" />
        </pattern>
      </defs>
    </svg>

    <!-- Landscape Elements -->
    <div id="landscape" class="landscape-container">
        <!-- Sun/Moon -->
        <div id="celestial"></div>
        
        <!-- Weather Layer (Clouds/Rain/Stars) -->
        <div id="weather-layer" class="weather-layer"></div>
        
        <!-- Vector Mountains mimicking reference image -->
        <svg viewBox="0 0 1440 800" preserveAspectRatio="xMidYMax slice" class="absolute bottom-0 w-full h-full pointer-events-none" style="z-index: 3;">
          <!-- Layer 1 (Furthest) -->
          <path d="M 0,640 L 150,588 L 280,628 L 550,572 L 800,640 L 1100,580 L 1350,620 L 1440,600 L 1440,820 L 0,820 Z" fill="var(--m1)" class="mountain-path" />
          
          <!-- Layer 2 -->
          <path d="M 0,660 L 150,652 L 450,560 L 800,652 L 1150,612 L 1440,660 L 1440,820 L 0,820 Z" fill="var(--m2)" class="mountain-path" />
          
          <!-- Layer 3 -->
          <path d="M 0,720 L 250,668 L 450,692 L 850,600 L 1200,680 L 1440,640 L 1440,820 L 0,820 Z" fill="var(--m3)" class="mountain-path" />
          
          <!-- Layer 4 -->
          <path d="M 0,740 Q 250,700 500,740 T 1100,700 T 1440,760 L 1440,820 L 0,820 Z" fill="var(--m4)" class="mountain-path" />
          
          <!-- Layer 5 (Foreground Hills + Trees) -->
          <path d="M 0,750 Q 400,730 800,740 T 1440,735 L 1440,820 L 0,820 Z" fill="var(--m5)" class="mountain-path" />
          <!-- Repeating pine trees on top of the hills -->
          <rect x="0" y="700" width="1440" height="120" fill="url(#tree-pattern)" />
        </svg>
    </div>

    <!-- Login UI Overlay -->
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative z-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-6 text-4xl font-extrabold tracking-tight dynamic-text drop-shadow-md">Welcome Back</h2>
            <p class="mt-2 text-sm font-medium dynamic-text-muted">Sign in to your account to continue</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="glass-card py-10 px-6 sm:rounded-2xl sm:px-10">
                @if(session()->has('loginError'))
                    <div class="mb-6 rounded-xl bg-red-50/90 backdrop-blur-md border border-red-200 p-4 shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('loginError') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="login" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-semibold dynamic-text mb-1.5">Username</label>
                        <div>
                            <input id="username" name="username" type="text" required autofocus
                                   class="glass-input block w-full appearance-none rounded-xl px-4 py-3 shadow-sm focus:outline-none sm:text-sm"
                                   placeholder="Enter your username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold dynamic-text mb-1.5">Password</label>
                        <div>
                            <input id="password" name="password" type="password" required
                                   class="glass-input block w-full appearance-none rounded-xl px-4 py-3 shadow-sm focus:outline-none sm:text-sm"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="flex w-full justify-center rounded-xl border border-transparent bg-blue-600 py-3 px-4 text-sm font-bold text-white shadow-lg hover:bg-blue-700 hover:shadow-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            Sign in
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-400/20 bottom-links">
                    <div class="flex items-center justify-center space-x-4 text-sm font-medium dynamic-text-muted">
                        <a href="kirim-email" class="hover:text-blue-500 transition-colors">Forgot password?</a>
                        <span class="opacity-50">•</span>
                        <a href="register-guest" class="hover:text-blue-500 transition-colors">Register</a>
                        <span class="opacity-50">•</span>
                        <a href="logbook" class="hover:text-blue-500 transition-colors">View as Guest</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hour = new Date().getHours();
            const weatherLayer = document.getElementById('weather-layer');
            const celestial = document.getElementById('celestial');
            
            // Determine theme based on actual time
            let theme = 'night';
            if (hour >= 6 && hour < 16) {
                theme = 'day';
            } else if (hour >= 16 && hour < 19) {
                theme = 'rain'; // Afternoon rain effect
            }
            
            // Apply theme class
            document.body.className = `theme-${theme}`;
            
            // Generate Weather Effects
            if (theme === 'day') {
                celestial.className = 'celestial-body sun';
                for(let i=0; i<6; i++) {
                    let wrapper = document.createElement('div');
                    wrapper.className = 'cloud-wrapper';
                    
                    let cloud = document.createElement('div');
                    cloud.className = 'cloud';
                    
                    // Random scale
                    let scale = 0.5 + Math.random() * 0.8;
                    wrapper.style.transform = `scale(${scale})`;
                    
                    wrapper.style.top = (Math.random() * 55) + 'vh';
                    wrapper.style.left = '-300px';
                    wrapper.style.animationDuration = (40 + Math.random() * 80) + 's';
                    wrapper.style.animationDelay = (Math.random() * -80) + 's';
                    wrapper.style.opacity = (0.7 + Math.random() * 0.3);
                    
                    wrapper.appendChild(cloud);
                    weatherLayer.appendChild(wrapper);
                }
            } else if (theme === 'rain') {
                for(let i=0; i<150; i++) {
                    let drop = document.createElement('div');
                    drop.className = 'drop';
                    drop.style.left = Math.random() * 110 + 'vw';
                    drop.style.animationDuration = (0.3 + Math.random() * 0.4) + 's';
                    drop.style.animationDelay = Math.random() * 2 + 's';
                    drop.style.opacity = (0.2 + Math.random() * 0.8);
                    weatherLayer.appendChild(drop);
                }
            } else if (theme === 'night') {
                celestial.className = 'celestial-body moon';
                for(let i=0; i<250; i++) {
                    let star = document.createElement('div');
                    star.className = 'star';
                    let size = (1 + Math.random() * 2) + 'px';
                    star.style.width = size;
                    star.style.height = size;
                    star.style.top = Math.random() * 65 + 'vh';
                    star.style.left = Math.random() * 100 + 'vw';
                    star.style.animationDuration = (2 + Math.random() * 5) + 's';
                    star.style.animationDelay = Math.random() * 5 + 's';
                    weatherLayer.appendChild(star);
                }
            }
        });
    </script>
</body>
</html>
