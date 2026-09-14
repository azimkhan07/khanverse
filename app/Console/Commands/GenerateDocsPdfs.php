<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class GenerateDocsPdfs extends Command
{
    protected $signature = 'docs:generate-pdfs';

    protected $description = 'Render roadmap/listing HTML docs in docs/ to PDF and CSV into public/docs/';

    public function handle(): int
    {
        $docsDir = File::exists(base_path('docs')) ? base_path('docs') : public_path('api-docs');

        $outDir = public_path('docs');
        File::ensureDirectoryExists($outDir);

        $files = [
            'phase-2-listing' => 'SkillNest-Phase2-Listing-Checklist.pdf',
            'phase-2-roadmap' => 'SkillNest-Phase2-Feature-Roadmap.pdf',
            'server-configuration-roadmap' => 'SkillNest-Server-Configuration-Roadmap.pdf',
        ];

        foreach ($files as $stem => $pdfName) {
            $html = File::get($docsDir.'/'.$stem.'.html');
            $pdf = Pdf::loadHTML($html, 'UTF-8');
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->save($outDir.'/'.$pdfName);
            $this->info("PDF → public/docs/{$pdfName}");
        }

        $csv = $outDir.'/SkillNest-Phase2-Listing.csv';
        $rows = [
            ['Category', 'Item', 'What/Who', 'Why', 'How', 'Effort'],
            ['Infra', 'Production queue + workers', 'Redis queue + Supervisor workers', 'Reliable email/webhook/AI jobs (today queue=sync blocks)', 'QUEUE_CONNECTION=redis, queue:work under supervisor; optional Horizon', 'M'],
            ['Infra', 'Scheduler cron', 'artisan schedule:run each minute', 'Escrow timers, availability rollover, reminders', 'cron * * * * * php artisan schedule:run', 'S'],
            ['Infra', 'Webhook relay (outgoing)', 'Push order/payment events to sellers/clients', 'Real-time alerts (Telegram/Discord/CRM)', 'WebhookDeliveryService + webhook_deliveries table + HMAC', 'M'],
            ['Infra', 'Webhook ingress (incoming)', 'Accept third-party callbacks', 'Payment IPN, eKYC, SMS/WhatsApp DLR', 'Signed routes (CSRF whitelist) + HMAC + idempotent jobs', 'M'],
            ['Infra', 'Object storage / CDN', 'KYC docs, order media, gig images', 'Uploads scale; CDN speeds images', 'MinIO / S3-compatible + Cloudflare/Bunny', 'M'],
            ['Infra', 'Search engine', 'Gig/seller search', 'Replace LIKE with typo-tolerant local search', 'Meilisearch/OpenSearch + jobs', 'M'],
            ['Infra', 'AI gateway', 'Single LLM access point', 'Suggestions/quotes/translation/moderate/triage', 'ai-service (FastAPI) + API keys + cost guardrails', 'S-M'],
            ['Infra', 'Monitoring & alerting', 'Sentry/Flare + Laravel health + UptimeRobot', 'Know when payments/webhooks break', 'SDK + dashboards + alerting', 'S'],
            ['Infra', 'Redis cache + session', 'cache=file/session=file today', 'Multi-server ready, faster', 'REDIS config + drivers', 'S'],
            ['Infra', 'CI/CD', 'GitHub Actions + deploy script', 'Safe releases of webhooks/microservices/AI', 'lint→build→migrate--force→caches→queue:restart', 'M'],
            ['Infra', 'Logging pipeline', 'JSON logs + request_id', 'Debug webhook chains + AI usage', 'Loki/S3 ship', 'S'],
            ['Microservice', 'notification-service', 'Email + SMS + WhatsApp + push', 'India-first delivery (email is invisible)', 'Laravel/Node + MSG91/Fast2SMS + WABA + OneSignal', 'M'],
            ['Microservice', 'ai-service', 'LLM + STT features', 'Suggestions, quote, translation, triage, moderation, copilot', 'FastAPI + OpenAI/Anthropic/Gemini + Whisper/Ollama', 'M'],
            ['Microservice', 'search-service (opt)', 'Index/query gigs + sellers', 'Fast, typo-tolerant, geo', 'Meilisearch/OpenSearch behind API', 'M'],
            ['Microservice', 'media-service (opt)', 'Images/PDF processing', 'Thumbnails, watermarks at scale', 'ImageMagick/ffmpeg workers', 'M'],
            ['Microservice', 'payment-service (opt)', 'Extract PaymentGatewayManager', 'Independent payments deploys, multi-currency later', 'Keep driver shape, own deploy', 'M'],
            ['AI', 'Service suggestions', 'Recommend gigs by profile+history', 'Boosts conversion (P1 stub exists)', 'Recommendation endpoint + Redis profile + analytics', 'M'],
            ['AI', 'Auto-quote assistant', 'Requirement→price+scope+ETA', 'Fair instant quote for buyers + sellers', 'LLM + catalog + price history', 'M'],
            ['AI', 'Hindi/Hinglish translation', 'Listings + requirements in 2 languages', 'India-first, nobody serves this', 'LLM translate + cache', 'M'],
            ['AI', 'Voice-to-requirement', 'Speak instead of type', 'Non-technical users', 'Whisper STT → requirement builder', 'M'],
            ['AI', 'Smart matching', 'Best seller for job', 'Higher trust + retention', 'RAG + click/conv signals', 'L'],
            ['AI', 'Moderation & fraud triage', 'Scam detection + dispute first-look', 'Safety at scale cheaply', 'Classifiers + escalate to admin', 'M'],
            ['AI', 'Pricing intelligence', 'Suggested price per category', 'Non-tech sellers win', 'Aggregate completed orders', 'M'],
            ['AI', 'Support copilot', 'Answer FAQs/disputes instantly', 'Cut support cost', 'Support KB + LLM', 'M'],
            ['Webhook Event', 'order.created', 'Buyer places order', 'Seller alert + client CRM', 'Relay via queue', 'S'],
            ['Webhook Event', 'order.paid/payment.captured', 'Gateway callback', 'Notify seller + escrow start', 'in+out', 'S'],
            ['Webhook Event', 'order.accepted/delivered/cancelled', 'Status changes', 'SMS/WhatsApp + client webhook', 'out', 'S'],
            ['Webhook Event', 'project.milestone', 'Milestone delivered', 'Timeline push', 'out', 'S'],
            ['Webhook Event', 'kyc.status', 'Admin verifies/rejects', 'Re-upload flow + trust', 'in', 'S'],
            ['Webhook Event', 'sms.dlr/whatsapp.delivery', 'Provider callbacks', 'Retry logic', 'in', 'S'],
            ['Webhook Event', 'ekyc.webhook', 'DigiLocker/UIDAI result', 'Aadhaar eKYC trust', 'in', 'M'],
            ['Integration', 'SMS', 'Order + OTP alerts', 'Local sellers check SMS', 'MSG91/Fast2SMS/Twilio', 'S'],
            ['Integration', 'WhatsApp Business API', 'Chat + order notifications', 'Primary India channel', 'Meta WABA via Gupshup/360dialog', 'M'],
            ['Integration', 'Payments expand', 'UPI/cards/netbanking/EMI', 'More rails, lower fees', 'Razorpay/Cashfree/Stripe drivers', 'M'],
            ['Integration', 'eKYC', 'Seller verification', 'Trust differentiator', 'DigiLocker API + consent OTP', 'M'],
            ['Integration', 'E-tax/invoice', 'GST invoices, TDS', 'Compliance for services', 'IRP e-invoice/Zoho', 'M'],
            ['Integration', 'AI providers', 'LLM + STT', 'All AI features', 'OpenAI/Anthropic/Gemini + local Ollama', 'S'],
        ];

        $fp = fopen($csv, 'w');
        foreach ($rows as $r) {
            fputcsv($fp, array_map(fn ($v) => mb_convert_encoding($v, 'UTF-8', 'UTF-8'), $r));
        }
        fclose($fp);
        $this->info("CSV → public/docs/SkillNest-Phase2-Listing.csv");

        return self::SUCCESS;
    }
}