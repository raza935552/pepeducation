<x-public-layout title="Terms of Service">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Terms of Service
            </h1>
            <p class="text-gray-500">
                Last updated: {{ date('F j, Y') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 lg:p-12 border border-cream-200">
                <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-600 prose-li:text-gray-600">

                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using PepProfesor, you accept and agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our website.</p>

                    <h2>2. Description of Service</h2>
                    <p>PepProfesor provides educational information about peptides for research purposes. We aggregate and present research data, dosing calculators, and community-contributed content.</p>

                    <h2>3. Educational Purpose Only</h2>
                    <p>All content on PepProfesor is for educational and informational purposes only. The information provided:</p>
                    <ul>
                        <li>Is not intended as medical advice</li>
                        <li>Should not be used for self-diagnosis or self-treatment</li>
                        <li>Does not replace consultation with qualified healthcare professionals</li>
                        <li>Is provided "as is" without warranties of any kind</li>
                    </ul>

                    <h2>4. User Accounts</h2>
                    <p>When you create an account, you agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information</li>
                        <li>Maintain the security of your account credentials</li>
                        <li>Accept responsibility for all activities under your account</li>
                        <li>Notify us immediately of any unauthorized access</li>
                    </ul>

                    <h2>5. User Contributions</h2>
                    <p>If you contribute content (such as edit suggestions), you:</p>
                    <ul>
                        <li>Grant us a non-exclusive license to use, modify, and display your contributions</li>
                        <li>Warrant that your contributions are accurate to the best of your knowledge</li>
                        <li>Agree not to submit false, misleading, or harmful content</li>
                        <li>Understand that we may reject or modify contributions at our discretion</li>
                    </ul>

                    <h2>6. Prohibited Uses</h2>
                    <p>You agree not to:</p>
                    <ul>
                        <li>Use the service for any illegal purpose</li>
                        <li>Attempt to gain unauthorized access to our systems</li>
                        <li>Interfere with the proper working of the website</li>
                        <li>Scrape or harvest data without permission</li>
                        <li>Impersonate others or misrepresent your affiliation</li>
                    </ul>

                    <h2>7. Intellectual Property</h2>
                    <p>The content, design, and functionality of PepProfesor are protected by intellectual property laws. You may not copy, modify, or distribute our content without permission, except for personal, non-commercial use.</p>

                    <h2>8. Limitation of Liability</h2>
                    <p>PepProfesor and its operators shall not be liable for any direct, indirect, incidental, special, or consequential damages arising from your use of or inability to use our services.</p>

                    <h2>9. Changes to Terms</h2>
                    <p>We may modify these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>

                    <h2>10. Contact</h2>
                    <p>For questions about these Terms, please contact us through our <button onclick="Livewire.dispatch('openContactModal')" class="text-gold-600 hover:underline">contact form</button>.</p>

                </div>
            </div>
        </div>
    </section>
</x-public-layout>
