<x-public-layout
    title="Pep Guide"
    description="Your personal peptide guide. Ask questions, get recommendations, and learn everything about peptides."
>
    <div class="pep-guide-wrapper">
        <iframe
            src="https://cdn.botpress.cloud/webchat/v3.6/shareable.html?configUrl=https://files.bpcontent.cloud/2026/03/12/13/20260312130124-9KCFPGK7.json"
            title="Pep Guide"
            allow="microphone"
        ></iframe>
    </div>

    <style>
        .pep-guide-wrapper {
            width: 100%;
            height: calc(100vh - 65px);
            position: relative;
        }
        .pep-guide-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        /* Hide the footer on this page */
        footer { display: none !important; }
    </style>
</x-public-layout>
