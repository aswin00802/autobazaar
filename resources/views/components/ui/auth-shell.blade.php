@props([
    'heading',              // the big line on the green panel
    'lede' => null,         // one supporting sentence
    'points' => [],         // [[icon, text], ...] shown as ticks under the lede
    'footnote' => null,     // small print at the bottom of the form column
])

{{--
    Shared frame for the customer sign-in, sign-up and OTP screens.

    Left: a green brand panel (hidden on phones, where it would just push the
    form below the fold). Right: whatever the page puts in the slot.

    Only these three pages use this component.
--}}

<style>
    .ab-auth { display: grid; gap: 0; max-width: 980px; margin: 0 auto; overflow: hidden; }
    @media (min-width: 1024px) { .ab-auth { grid-template-columns: 1fr 1fr; } }

    .ab-auth-brand {
        display: none;
        position: relative;
        padding: 40px 36px;
        color: #fff;
        background: linear-gradient(150deg, #0B5D3B 0%, #0E7A4D 55%, #0B5D3B 100%);
    }
    @media (min-width: 1024px) { .ab-auth-brand { display: flex; flex-direction: column; } }

    /* Soft light behind the copy, so the flat green does not look like a block of paint. */
    .ab-auth-brand::after {
        content: ''; position: absolute; right: -70px; top: -70px; width: 260px; height: 260px;
        border-radius: 50%; background: rgba(255, 255, 255, .08);
    }
    .ab-auth-brand > * { position: relative; z-index: 1; }

    .ab-auth-brand h2 { font-size: 26px; line-height: 1.25; font-weight: 800; letter-spacing: -.01em; }
    .ab-auth-brand p.lede { margin-top: 10px; font-size: 14px; line-height: 1.65; color: rgba(255, 255, 255, .82); }

    .ab-auth-points { margin-top: 26px; display: grid; gap: 14px; }
    .ab-auth-points li { display: flex; gap: 11px; align-items: flex-start; font-size: 13px; line-height: 1.5; }
    .ab-auth-points .tick {
        flex: none; display: grid; place-items: center; width: 26px; height: 26px;
        border-radius: 8px; background: rgba(255, 255, 255, .14); color: #fff;
    }

    .ab-auth-mark {
        margin-top: auto; padding-top: 28px; font-size: 12px; color: rgba(255, 255, 255, .7);
    }

    .ab-auth-form { padding: 32px 24px; }
    @media (min-width: 640px) { .ab-auth-form { padding: 40px; } }

    /* +91 sits inside the field so the box still reads as one control. */
    .ab-phone { position: relative; }
    .ab-phone .cc {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        font-size: 14px; font-weight: 600; color: #6B7671; pointer-events: none;
    }
    .ab-phone .ab-field { padding-left: 50px; letter-spacing: .04em; }

    /* One message line for every outcome; colour says which. */
    .ab-auth-msg { margin-top: 2px; border-radius: 10px; padding: 10px 12px; font-size: 13px; font-weight: 500; }
    .ab-auth-msg.is-error { background: #FEF2F2; color: #B42318; }
    .ab-auth-msg.is-ok { background: #ECFDF3; color: #0B5D3B; }

    .ab-auth-busy { opacity: .65; pointer-events: none; }
</style>

<section class="ab-container py-8 lg:py-14">
    <div class="ab-auth ab-card">

        <aside class="ab-auth-brand">
            <h2>{{ $heading }}</h2>

            @if ($lede)
                <p class="lede">{{ $lede }}</p>
            @endif

            @if ($points)
                <ul class="ab-auth-points">
                    @foreach ($points as [$icon, $text])
                        <li>
                            <span class="tick"><x-ui.icon :name="$icon" :size="14" /></span>
                            <span>{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <p class="ab-auth-mark">{{ $footnote ?? 'Your number is used only to sign you in and to reach you about your enquiries.' }}</p>
        </aside>

        <div class="ab-auth-form">
            {{ $slot }}
        </div>
    </div>
</section>
