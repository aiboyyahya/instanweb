<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ContactVerificationService;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use RuntimeException;
use Tests\TestCase;

class VerificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_numbers_are_normalized_to_international_format(): void
    {
        $sms = new SmsService;

        $this->assertSame('6281234567890', $sms->formatPhoneNumber('081234567890'));
        $this->assertSame('6281234567890', $sms->formatPhoneNumber('+62 812-3456-7890'));
        $this->assertSame('6281234567890', $sms->formatPhoneNumber('81234567890'));
        $this->assertSame('6281234567890', $sms->formatPhoneNumber('62081234567890'));
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SmsService)->formatPhoneNumber('123');
    }

    public function test_http_driver_sends_expected_payload(): void
    {
        config([
            'services.sms.driver' => 'http',
            'services.sms.endpoint' => 'https://sms.test/send',
            'services.sms.token' => 'secret-token',
            'services.sms.sender' => 'Toko Uji',
        ]);

        Http::fake([
            'sms.test/*' => Http::response(['ok' => true]),
        ]);

        (new SmsService)->send('081234567890', 'Halo pelanggan');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://sms.test/send'
                && $request->hasHeader('Authorization', 'Bearer secret-token')
                && $request['to'] === '6281234567890'
                && $request['target'] === '6281234567890'
                && $request['sender'] === 'Toko Uji'
                && $request['message'] === 'Halo pelanggan';
        });
    }

    public function test_twilio_driver_sends_expected_request(): void
    {
        config([
            'services.sms.driver' => 'twilio',
            'services.sms.twilio.sid' => 'AC123',
            'services.sms.twilio.token' => 'twilio-token',
            'services.sms.twilio.from' => '+14155550100',
        ]);

        Http::fake([
            'api.twilio.com/*' => Http::response(['sid' => 'SM123']),
        ]);

        (new SmsService)->send('081234567890', 'Halo pelanggan');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/2010-04-01/Accounts/AC123/Messages.json')
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('AC123:twilio-token'))
                && $request['To'] === '+6281234567890'
                && $request['From'] === '+14155550100'
                && $request['Body'] === 'Halo pelanggan';
        });
    }

    public function test_vonage_driver_sends_expected_request(): void
    {
        config([
            'services.sms.driver' => 'vonage',
            'services.sms.vonage.key' => 'vonage-key',
            'services.sms.vonage.secret' => 'vonage-secret',
            'services.sms.vonage.from' => 'Instanweb',
        ]);

        Http::fake([
            'rest.nexmo.com/*' => Http::response(['messages' => [['status' => '0']]]),
        ]);

        (new SmsService)->send('081234567890', 'Halo pelanggan');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://rest.nexmo.com/sms/json'
                && $request['api_key'] === 'vonage-key'
                && $request['api_secret'] === 'vonage-secret'
                && $request['from'] === 'Instanweb'
                && $request['to'] === '6281234567890'
                && $request['text'] === 'Halo pelanggan';
        });
    }

    public function test_gateway_failure_is_reported(): void
    {
        config([
            'services.sms.driver' => 'http',
            'services.sms.endpoint' => 'https://sms.test/send',
        ]);

        Http::fake([
            'sms.test/*' => Http::response(['error' => 'rejected'], 422),
        ]);

        $this->expectException(RuntimeException::class);

        (new SmsService)->send('081234567890', 'Halo');
    }

    public function test_production_rejects_log_sms_driver(): void
    {
        $this->app['env'] = 'production';
        config(['services.sms.driver' => 'log']);

        $this->expectException(RuntimeException::class);

        (new SmsService)->send('081234567890', 'Halo');
    }

    public function test_production_rejects_log_mail_driver_for_verification_email(): void
    {
        $this->app['env'] = 'production';
        config(['mail.default' => 'log']);

        $user = User::factory()->unverified()->create();

        $this->expectException(RuntimeException::class);

        app(ContactVerificationService::class)->sendEmailCode($user);
    }

    public function test_google_login_only_marks_email_verified_when_google_confirms_it(): void
    {
        $this->fakeGoogleUser('google-unverified@example.com', verifiedEmail: false);

        $this->get(route('google.callback'))->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'google-unverified@example.com',
            'email_verified_at' => null,
        ]);
    }

    public function test_google_login_marks_email_verified_when_google_confirms_it(): void
    {
        $this->fakeGoogleUser('google-verified@example.com', verifiedEmail: true);

        $this->get(route('google.callback'))->assertRedirect('/');

        $this->assertDatabaseMissing('users', [
            'email' => 'google-verified@example.com',
            'email_verified_at' => null,
        ]);
    }

    protected function fakeGoogleUser(string $email, bool $verifiedEmail): void
    {
        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
        ]);

        $googleUser = (new SocialiteUser)->setRaw(['verified_email' => $verifiedEmail])->map([
            'id' => 'google-123',
            'name' => 'Pengguna Google',
            'email' => $email,
        ]);

        $provider = \Mockery::mock(Provider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}
