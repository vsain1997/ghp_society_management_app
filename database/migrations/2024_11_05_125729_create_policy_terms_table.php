<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('policy_terms', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('content');
            $table->integer('society_id')->default(0);
            $table->timestamps();
        });

        // Insert default data
        DB::table('policy_terms')->insert([
            [
                'id' => 1,
                'name' => 'privacy_policy',
                'content' => '<h1 style="color: #333; font-size: 2em; font-family: Arial, sans-serif; margin-bottom: 10px;">Privacy Policy</h1>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">Last updated: November 5, 2024</p>

<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Welcome to our Privacy Policy. Your privacy is critically important to us. This policy explains how we collect, use, and share information about you when you use our services.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">1. Information We Collect</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum vestibulum. Cras venenatis euismod malesuada.
</p>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    We may collect personal identification information (Name, Email Address, etc.) and non-personal identification information (Browser type, IP address, etc.) whenever you interact with our site.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">2. How We Use Your Information</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vitae mauris felis. Nullam sed velit id ligula interdum venenatis. Ut eu nunc nec turpis congue egestas.
</p>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Your information helps us to respond to your customer service requests, improve our site, and enhance your experience with us.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">3. Sharing Your Information</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin suscipit, est a varius convallis, lorem urna condimentum leo, nec luctus lorem orci non quam.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">4. Contact Us</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    If you have any questions about this Privacy Policy, please contact us at example@example.com.
</p>
',
                'society_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'terms_use',
                'content' => '<h1 style="color: #333; font-size: 2em; font-family: Arial, sans-serif; margin-bottom: 10px;">Terms of Use</h1>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">Last updated: November 5, 2024</p>

<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Welcome to our Terms of Use. These terms govern your access to and use of our website and services. By accessing or using our site, you agree to these terms in full.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">1. Acceptance of Terms</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac volutpat dui. By accessing our website, you confirm that you accept these terms and conditions and agree to comply with them.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">2. Changes to Terms</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    We reserve the right to modify these Terms at any time. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vitae justo neque. If we make material changes, we will provide you with appropriate notice.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">3. User Responsibilities</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    As a user, you agree not to misuse the service. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce sit amet ligula vel arcu aliquam tincidunt in ut lacus.
</p>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    You agree not to use the website in a way that may impair its performance, corrupt its content, or otherwise reduce its functionality.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">4. Intellectual Property</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    All content, trademarks, and data on this site, including but not limited to text, graphics, and icons, are our property or property of their respective owners. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">5. Limitation of Liability</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    To the fullest extent permitted by law, we shall not be liable for any damages arising from your use of this website. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In hac habitasse platea dictumst.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">6. Governing Law</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    These Terms of Use are governed by and construed in accordance with the laws of [Your Country/State]. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</p>

<h2 style="color: #333; font-size: 1.5em; font-family: Arial, sans-serif; margin-top: 30px; margin-bottom: 10px;">7. Contact Information</h2>
<p style="font-family: Arial, sans-serif; font-size: 1em; line-height: 1.6; margin-bottom: 20px;">
    If you have any questions about these Terms of Use, please contact us at example@example.com.
</p>
',
                'society_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_terms');
    }
};
