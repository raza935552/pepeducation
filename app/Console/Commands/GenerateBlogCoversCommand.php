<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateBlogCoversCommand extends Command
{
    protected $signature = 'blog:generate-covers {--force : Regenerate even if a featured_image already exists}';

    protected $description = 'Generate branded 1200x630 PNG cover images for blog posts that have no featured_image';

    private const W = 1200;
    private const H = 630;

    private const FONT_BOLD    = '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf';
    private const FONT_REGULAR = '/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf';

    public function handle(): int
    {
        $outDir = public_path('blog-covers');
        if (!File::exists($outDir)) {
            File::makeDirectory($outDir, 0755, true);
        }

        $query = BlogPost::query();
        if (!$this->option('force')) {
            $query->where(fn ($q) => $q->whereNull('featured_image')->orWhere('featured_image', ''));
        }

        $posts = $query->with('categories')->get();
        $this->info('Generating covers for '.$posts->count().' posts...');

        $generated = 0;
        $skipped   = 0;

        foreach ($posts as $post) {
            $cat   = $post->categories->first();
            $color = $cat?->color ?? '#475569';
            $catName = $cat?->name ?? 'Peptide Research';

            $path = $outDir.'/'.$post->slug.'.png';

            try {
                $this->drawCover($path, $post->title, $catName, $color);
                $publicUrl = '/blog-covers/'.$post->slug.'.png';
                $post->update(['featured_image' => $publicUrl]);
                $generated++;
                $this->line("  + {$post->slug}");
            } catch (\Throwable $e) {
                $skipped++;
                $this->error('  ! '.$post->slug.' - '.$e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Generated: {$generated} | Skipped: {$skipped}");

        return self::SUCCESS;
    }

    private function drawCover(string $path, string $title, string $category, string $hexColor): void
    {
        $img = imagecreatetruecolor(self::W, self::H);
        imageantialias($img, true);

        [$r, $g, $b] = $this->hexToRgb($hexColor);

        // Dark navy base background
        $bg = imagecolorallocate($img, 15, 23, 42);
        imagefilledrectangle($img, 0, 0, self::W, self::H, $bg);

        // Diagonal accent shape using category color (semi-transparent)
        $accent = imagecolorallocatealpha($img, $r, $g, $b, 90);
        $points = [
            self::W * 0.55, 0,
            self::W, 0,
            self::W, self::H * 0.7,
            self::W * 0.30, self::H,
            self::W * 0.55, self::H * 0.4,
        ];
        imagefilledpolygon($img, $points, 5, $accent);

        // Soft circle bloom in top-left
        $bloom = imagecolorallocatealpha($img, $r, $g, $b, 110);
        imagefilledellipse($img, 100, 80, 380, 380, $bloom);

        // Subtle dotted pattern for texture
        $dot = imagecolorallocatealpha($img, 255, 255, 255, 115);
        for ($x = 0; $x < self::W; $x += 36) {
            for ($y = 0; $y < self::H; $y += 36) {
                imagefilledellipse($img, $x, $y, 2, 2, $dot);
            }
        }

        // Top brand strip
        $catBg = imagecolorallocate($img, $r, $g, $b);
        imagefilledrectangle($img, 60, 60, 60 + 8, 60 + 36, $catBg);

        $catText = imagecolorallocate($img, 226, 232, 240);
        imagettftext($img, 14, 0, 80, 86, $catText, self::FONT_BOLD, mb_strtoupper($category, 'UTF-8'));

        // Title text - wrap and render
        $titleColor = imagecolorallocate($img, 255, 255, 255);
        $this->drawWrappedText($img, $title, 60, 180, self::W - 380, 44, $titleColor, self::FONT_BOLD);

        // Brand mark bottom-left
        $brandColor = imagecolorallocate($img, 148, 163, 184);
        $accentBrand = imagecolorallocate($img, $r, $g, $b);
        imagettftext($img, 18, 0, 60, self::H - 50, $accentBrand, self::FONT_BOLD, 'Professor');
        imagettftext($img, 18, 0, 195, self::H - 50, $brandColor, self::FONT_BOLD, 'Peptides');
        imagettftext($img, 12, 0, 60, self::H - 28, $brandColor, self::FONT_REGULAR, 'professorpeptides.co');

        // "READ NOW" pill bottom-right
        $pillBg = imagecolorallocatealpha($img, $r, $g, $b, 60);
        imagefilledrectangle($img, self::W - 200, self::H - 80, self::W - 60, self::H - 35, $pillBg);
        imagettftext($img, 13, 0, self::W - 175, self::H - 50, $titleColor, self::FONT_BOLD, 'READ ARTICLE >');

        imagepng($img, $path, 6);
        imagedestroy($img);
    }

    private function drawWrappedText($img, string $text, int $x, int $y, int $maxWidth, int $size, int $color, string $font): void
    {
        $words = preg_split('/\s+/', $text);
        $lines = [];
        $line  = '';

        foreach ($words as $word) {
            $candidate = $line === '' ? $word : $line.' '.$word;
            $box = imagettfbbox($size, 0, $font, $candidate);
            $width = abs($box[2] - $box[0]);
            if ($width > $maxWidth && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') {
            $lines[] = $line;
        }

        // Cap to 4 lines, truncate last with ellipsis if needed
        if (count($lines) > 4) {
            $lines = array_slice($lines, 0, 4);
            $lines[3] = rtrim($lines[3], '. ').'...';
        }

        $lineHeight = $size + 18;
        foreach ($lines as $i => $ln) {
            imagettftext($img, $size, 0, $x, $y + ($i * $lineHeight), $color, $font, $ln);
        }
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
}
