{{-- Virtual card. Shows only what an admin issued/approved (last 4 + expiry). Never holds a full number or CVV. --}}
@props(['name' => '', 'brand' => ''])
@php
    $vc = \App\Models\VirtualCard::where('user_id', Auth::id())->first();
    $vcStatus = $vc ? $vc->status : 'none';
@endphp

<style>
    .vcard-row { display: flex; justify-content: center; margin: 4px 0 24px; }
    @media (min-width: 992px) { .vcard-row { justify-content: flex-start; } }
    .vcard {
        container-type: inline-size;
        position: relative;
        width: min(100%, 360px);
        aspect-ratio: 1.586 / 1;
        border-radius: 20px;
        padding: 6% 7%;
        box-sizing: border-box;
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(255,255,255,.16) 0%, rgba(255,255,255,0) 55%),
            linear-gradient(135deg, #5b2bd6 0%, #3b1a9a 55%, #25106b 100%);
        box-shadow: 0 14px 30px rgba(37, 16, 107, .35), inset 0 0 0 1px rgba(255,255,255,.08);
        overflow: hidden;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .vcard-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .vcard-brand { font-weight: 700; font-size: 4.6cqw; line-height: 1.15; letter-spacing: .04em; text-align: right;
        max-width: 62%; overflow-wrap: anywhere; }
    .vcard-chip { width: 14cqw; height: auto; display: block; }
    .vcard-number { font-family: "Courier New", ui-monospace, monospace; font-size: 6.4cqw;
        letter-spacing: .08em; white-space: nowrap; text-shadow: 0 1px 2px rgba(0,0,0,.35); }
    .vcard-meta { font-size: 2.8cqw; text-transform: uppercase; letter-spacing: .08em; opacity: .75; }
    .vcard-bottom { display: flex; justify-content: space-between; align-items: flex-end; gap: 8px; }
    .vcard-holder { font-size: 4.6cqw; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .vcard-bottom > div { flex: 1; min-width: 0; }
    .vcard.is-pending { filter: saturate(.55) brightness(.9); }
    .vcard-badge { font-size: 2.8cqw; font-weight: 700; letter-spacing: .12em; padding: .5em 1em;
        border: 1px solid rgba(255,255,255,.55); border-radius: 999px; white-space: nowrap; }
</style>

@if($vcStatus === 'approved' || $vcStatus === 'pending')
<div class="vcard-row">
    <div class="vcard {{ $vcStatus === 'pending' ? 'is-pending' : '' }}" role="img" aria-label="Virtual card for {{ $name }}">
        <div class="vcard-top">
            <svg class="vcard-chip" viewBox="0 0 48 36" aria-hidden="true">
                <defs><linearGradient id="vcg" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#f4f1ff"/><stop offset="1" stop-color="#a99be0"/></linearGradient></defs>
                <rect width="48" height="36" rx="6" fill="url(#vcg)"/>
                <path d="M0 13h14M0 23h14M34 13h14M34 23h14M17 0v36M31 0v36" stroke="#6a55c4" stroke-width="1.2" fill="none" opacity=".75"/>
                <rect x="17" y="11" width="14" height="14" rx="3" fill="none" stroke="#6a55c4" stroke-width="1.2" opacity=".75"/>
            </svg>
            <div class="vcard-brand">{{ $brand }}</div>
        </div>

        <div>
            <div class="vcard-number" aria-hidden="true">••••&nbsp;&nbsp;••••&nbsp;&nbsp;••••&nbsp;&nbsp;{{ $vcStatus === 'approved' ? $vc->last4 : '••••' }}</div>
            <div class="vcard-meta" style="margin-top:2.5cqw;">Valid thru &nbsp;{{ $vcStatus === 'approved' ? $vc->expiry : '--/--' }}</div>
        </div>

        <div class="vcard-bottom">
            <div>
                <div class="vcard-meta">Cardholder</div>
                <div class="vcard-holder">{{ $name }}</div>
            </div>
            @if($vcStatus === 'pending')<span class="vcard-badge">PENDING APPROVAL</span>@endif
        </div>
    </div>
</div>
@else
<div class="vcard-row">
    <div class="card p-3 shadow" style="width:min(100%,360px);">
        @if($vcStatus === 'rejected')
            <p class="mb-2"><small class="text-muted">Your last card request was not approved. You can submit a new request.</small></p>
        @else
            <p class="mb-2"><small class="text-muted">Get a virtual card linked to your account.</small></p>
        @endif
        <form method="POST" action="{{ route('virtualcard.request') }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Request virtual card</button>
        </form>
    </div>
</div>
@endif
