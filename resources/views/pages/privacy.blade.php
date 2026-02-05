<x-public-layout title="Privacy Policy">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 dark:from-brown-900 dark:to-brown-900 py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-cream-100 mb-4">
                Privacy Policy
            </h1>
            <p class="text-gray-500 dark:text-cream-400">
                Last updated: {{ date('F j, Y') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-brown-800 rounded-2xl p-8 lg:p-12 border border-cream-200 dark:border-brown-700">
                <div class="prose prose-lg dark:prose-invert max-w-none prose-headings:text-gray-900 dark:prose-headings:text-cream-100 prose-p:text-gray-600 dark:prose-p:text-cream-300 prose-li:text-gray-600 dark:prose-li:text-cream-300">

                    <h2>1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you:</p>
                    <ul>
                        <li>Create an account</li>
                        <li>Subscribe to our newsletter</li>
                        <li>Submit a contact form or peptide request</li>
                        <li>Contribute edits or suggestions</li>
                    </ul>
                    <p>This information may include your name, email address, and any other information you choose to provide.</p>

                    <h2>2. Automatically Collected Information</h2>
                    <p>When you access our website, we automatically collect certain information, including:</p>
                    <ul>
                        <li>IP address</li>
                        <li>Browser type and version</li>
                        <li>Operating system</li>
                        <li>Pages viewed and time spent</li>
                        <li>Referring website</li>
                    </ul>

                    <h2>3. How We Use Your Information</h2>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Provide, maintain, and improve our services</li>
                        <li>Send you technical notices and support messages</li>
                        <li>Send newsletters and marketing communications (with your consent)</li>
                        <li>Respond to your comments and questions</li>
                        <li>Monitor and analyze usage patterns</li>
                    </ul>

                    <h2>4. Information Sharing</h2>
                    <p>We do not sell, trade, or rent your personal information to third parties. We may share information in the following circumstances:</p>
                    <ul>
                        <li>With your consent</li>
                        <li>To comply with legal obligations</li>
                        <li>To protect our rights and safety</li>
                        <li>With service providers who assist our operations</li>
                    </ul>

                    <h2>5. Cookies</h2>
                    <p>We use cookies and similar technologies to:</p>
                    <ul>
                        <li>Remember your preferences (such as dark mode)</li>
                        <li>Understand how you use our website</li>
                        <li>Improve your experience</li>
                    </ul>
                    <p>You can control cookies through your browser settings.</p>

                    <h2>6. Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.</p>

                    <h2>7. Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access your personal information</li>
                        <li>Correct inaccurate information</li>
                        <li>Request deletion of your information</li>
                        <li>Opt-out of marketing communications</li>
                        <li>Withdraw consent at any time</li>
                    </ul>

                    <h2>8. Contact Us</h2>
                    <p>If you have questions about this Privacy Policy, please contact us through our <button onclick="Livewire.dispatch('openContactModal')" class="text-gold-600 dark:text-gold-400 hover:underline">contact form</button>.</p>

                </div>
            </div>
        </div>
    </section>
</x-public-layout>
