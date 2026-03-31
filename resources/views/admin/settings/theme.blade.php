<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Theme & Colors</span>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Settings
            </a>
        </div>
    </x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.theme.update') }}" method="POST" class="space-y-6" id="theme-form">
        @csrf
        @method('PUT')

        {{-- Color Pickers --}}
        <div class="card p-6 border-l-4 border-green-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Site Theme Colors
            </h3>
            <p class="text-sm text-gray-500 mb-6">Customize the 6 master colors that drive your site's look. Changes apply globally after saving.</p>

            @php
                $colorDefs = [
                    'primary'   => ['label' => 'Primary Color',    'desc' => 'Buttons, links, CTAs, active states'],
                    'secondary' => ['label' => 'Secondary Color',  'desc' => 'Gradients, badges, secondary accents'],
                    'bg'        => ['label' => 'Background Color', 'desc' => 'Page body, cards, surfaces'],
                    'heading'   => ['label' => 'Heading Color',    'desc' => 'h1-h6, section titles'],
                    'text'      => ['label' => 'Text Color',       'desc' => 'Body paragraphs, descriptions'],
                    'dark'      => ['label' => 'Dark/Footer Color','desc' => 'Footer background, dark sections'],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($colorDefs as $key => $def)
                    <div class="color-row" data-color-key="{{ $key }}">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">{{ $def['label'] }}</label>
                                <p class="text-xs text-gray-500">{{ $def['desc'] }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="color"
                                    class="color-picker w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5"
                                    value="{{ $colors[$key] }}"
                                    data-target="{{ $key }}">
                                <input type="text"
                                    name="{{ $key }}"
                                    class="color-hex w-28 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono text-sm text-center"
                                    value="{{ $colors[$key] }}"
                                    pattern="^#[0-9A-Fa-f]{6}$"
                                    maxlength="7"
                                    data-target="{{ $key }}">
                                <span class="text-xs text-gray-400 hidden sm:inline">Default: {{ $defaults[$key] }}</span>
                            </div>
                        </div>
                        {{-- Shade preview strip --}}
                        <div class="shade-strip flex gap-1 ml-0" data-shade-key="{{ $key }}">
                            @for($i = 0; $i < 11; $i++)
                                @php
                                    $shadeLabels = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="shade-box w-8 h-8 rounded" data-shade-index="{{ $shadeLabels[$i] }}"></div>
                                    <span class="text-[10px] text-gray-400 mt-0.5">{{ $shadeLabels[$i] }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            {{-- Reset to Defaults --}}
            <form action="{{ route('admin.settings.theme.reset') }}" method="POST" class="inline"
                  onsubmit="return confirm('Reset all theme colors to defaults? This cannot be undone.')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-red-50 text-red-700 border-red-200 hover:bg-red-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset to Defaults
                </button>
            </form>

            {{-- Save --}}
            <button type="submit" class="btn btn-primary">Save Theme</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Shade lightness mapping (mirrors ThemeService::SHADE_LIGHTNESS)
            const SHADE_LIGHTNESS = {
                50: 96, 100: 91, 200: 82, 300: 73, 400: 59,
                // 500 = original
                600: 37, 700: 28, 800: 19, 900: 12, 950: 7
            };
            const SHADE_LABELS = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

            // --- Color conversion helpers (mirror ThemeService) ---
            function hexToRgb(hex) {
                hex = hex.replace('#', '');
                if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
                return [
                    parseInt(hex.substring(0, 2), 16),
                    parseInt(hex.substring(2, 4), 16),
                    parseInt(hex.substring(4, 6), 16)
                ];
            }

            function rgbToHex(r, g, b) {
                return '#' + [r, g, b].map(c => Math.round(c).toString(16).padStart(2, '0')).join('');
            }

            function hexToHsl(hex) {
                let [r, g, b] = hexToRgb(hex);
                r /= 255; g /= 255; b /= 255;
                const max = Math.max(r, g, b), min = Math.min(r, g, b);
                let h, s, l = (max + min) / 2;

                if (max === min) {
                    return [0, 0, l * 100];
                }
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                    case g: h = ((b - r) / d + 2) / 6; break;
                    case b: h = ((r - g) / d + 4) / 6; break;
                }
                return [h * 360, s * 100, l * 100];
            }

            function hslToRgb(h, s, l) {
                h /= 360; s /= 100; l /= 100;
                if (s === 0) {
                    const v = Math.round(l * 255);
                    return [v, v, v];
                }
                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                function hue2rgb(p, q, t) {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1/6) return p + (q - p) * 6 * t;
                    if (t < 1/2) return q;
                    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                }
                return [
                    Math.round(hue2rgb(p, q, h + 1/3) * 255),
                    Math.round(hue2rgb(p, q, h) * 255),
                    Math.round(hue2rgb(p, q, h - 1/3) * 255)
                ];
            }

            function hslToHex(h, s, l) {
                const [r, g, b] = hslToRgb(h, s, l);
                return rgbToHex(r, g, b);
            }

            function generateShades(hex) {
                const [h, s, l] = hexToHsl(hex);
                const shades = {};

                for (const [shade, targetL] of Object.entries(SHADE_LIGHTNESS)) {
                    let satAdjust = 0;
                    if (targetL > l) {
                        satAdjust = Math.min(10, (targetL - l) * 0.15);
                    } else {
                        satAdjust = Math.max(-10, (targetL - l) * 0.1);
                    }
                    const newS = Math.max(0, Math.min(100, s + satAdjust));
                    shades[shade] = hslToHex(h, newS, targetL);
                }
                // 500 = original
                shades[500] = hex;
                return shades;
            }

            function updateShadeStrip(key, hex) {
                const strip = document.querySelector(`.shade-strip[data-shade-key="${key}"]`);
                if (!strip) return;
                const shades = generateShades(hex);
                SHADE_LABELS.forEach(label => {
                    const box = strip.querySelector(`.shade-box[data-shade-index="${label}"]`);
                    if (box) {
                        box.style.backgroundColor = shades[label];
                    }
                });
            }

            // --- Sync color picker <-> text input ---
            document.querySelectorAll('.color-picker').forEach(picker => {
                const key = picker.dataset.target;
                const hexInput = document.querySelector(`input.color-hex[data-target="${key}"]`);

                picker.addEventListener('input', function () {
                    hexInput.value = this.value.toUpperCase();
                    updateShadeStrip(key, this.value);
                });

                hexInput.addEventListener('input', function () {
                    let val = this.value;
                    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                        picker.value = val;
                        updateShadeStrip(key, val);
                    }
                });

                // Initialize shades on load
                updateShadeStrip(key, picker.value);
            });
        });
    </script>
</x-admin-layout>
