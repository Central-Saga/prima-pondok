<?php

use App\Models\Wisatawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $no_hp = null;
    public ?string $nationality = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('wisatawan'), 403);

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->no_hp = $user->wisatawan?->no_hp;
        $this->nationality = $user->wisatawan?->nationality;
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('wisatawan'), 403);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $wisatawan = $user->wisatawan ?: $user->wisatawan()->create([
            'name' => $user->name,
            'status' => 'active',
        ]);

        $wisatawan->update([
            'name' => $data['name'],
            'no_hp' => $data['no_hp'],
            'nationality' => $data['nationality'],
        ]);

        session()->flash('status', __('account.profile_saved'));
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('account.profile_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('account.profile_subtitle') }}</p>
            </div>
            <a href="{{ route('home') }}" class="ui-btn-secondary">{{ __('account.back_home') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="save" class="mt-6 space-y-4 ui-card">
            <div>
                <label class="ui-label">{{ __('account.name') }}</label>
                <input type="text" wire:model="name" class="ui-input" />
                @error('name') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="ui-label">{{ __('account.email') }}</label>
                <input type="email" wire:model="email" class="ui-input" />
                @error('email') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="ui-label">{{ __('account.phone') }}</label>
                    <input type="text" wire:model="no_hp" class="ui-input" placeholder="+62..." />
                    @error('no_hp') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
                <div x-data="{
                    open: false,
                    search: '',
                    selected: @entangle('nationality'),
                    countries: [
                        {name:'Afghanistan',code:'af'},{name:'Albania',code:'al'},{name:'Algeria',code:'dz'},{name:'Andorra',code:'ad'},{name:'Angola',code:'ao'},{name:'Antigua and Barbuda',code:'ag'},{name:'Argentina',code:'ar'},{name:'Armenia',code:'am'},{name:'Australia',code:'au'},{name:'Austria',code:'at'},
                        {name:'Azerbaijan',code:'az'},{name:'Bahamas',code:'bs'},{name:'Bahrain',code:'bh'},{name:'Bangladesh',code:'bd'},{name:'Barbados',code:'bb'},{name:'Belarus',code:'by'},{name:'Belgium',code:'be'},{name:'Belize',code:'bz'},{name:'Benin',code:'bj'},{name:'Bhutan',code:'bt'},
                        {name:'Bolivia',code:'bo'},{name:'Bosnia and Herzegovina',code:'ba'},{name:'Botswana',code:'bw'},{name:'Brazil',code:'br'},{name:'Brunei',code:'bn'},{name:'Bulgaria',code:'bg'},{name:'Burkina Faso',code:'bf'},{name:'Burundi',code:'bi'},{name:'Cabo Verde',code:'cv'},{name:'Cambodia',code:'kh'},
                        {name:'Cameroon',code:'cm'},{name:'Canada',code:'ca'},{name:'Central African Republic',code:'cf'},{name:'Chad',code:'td'},{name:'Chile',code:'cl'},{name:'China',code:'cn'},{name:'Colombia',code:'co'},{name:'Comoros',code:'km'},{name:'Congo (Brazzaville)',code:'cg'},{name:'Congo (Kinshasa)',code:'cd'},
                        {name:'Costa Rica',code:'cr'},{name:'Croatia',code:'hr'},{name:'Cuba',code:'cu'},{name:'Cyprus',code:'cy'},{name:'Czech Republic',code:'cz'},{name:'Denmark',code:'dk'},{name:'Djibouti',code:'dj'},{name:'Dominica',code:'dm'},{name:'Dominican Republic',code:'do'},{name:'Ecuador',code:'ec'},
                        {name:'Egypt',code:'eg'},{name:'El Salvador',code:'sv'},{name:'Equatorial Guinea',code:'gq'},{name:'Eritrea',code:'er'},{name:'Estonia',code:'ee'},{name:'Eswatini',code:'sz'},{name:'Ethiopia',code:'et'},{name:'Fiji',code:'fj'},{name:'Finland',code:'fi'},{name:'France',code:'fr'},
                        {name:'Gabon',code:'ga'},{name:'Gambia',code:'gm'},{name:'Georgia',code:'ge'},{name:'Germany',code:'de'},{name:'Ghana',code:'gh'},{name:'Greece',code:'gr'},{name:'Grenada',code:'gd'},{name:'Guatemala',code:'gt'},{name:'Guinea',code:'gn'},{name:'Guinea-Bissau',code:'gw'},
                        {name:'Guyana',code:'gy'},{name:'Haiti',code:'ht'},{name:'Honduras',code:'hn'},{name:'Hungary',code:'hu'},{name:'Iceland',code:'is'},{name:'India',code:'in'},{name:'Indonesia',code:'id'},{name:'Iran',code:'ir'},{name:'Iraq',code:'iq'},{name:'Ireland',code:'ie'},
                        {name:'Israel',code:'il'},{name:'Italy',code:'it'},{name:'Ivory Coast',code:'ci'},{name:'Jamaica',code:'jm'},{name:'Japan',code:'jp'},{name:'Jordan',code:'jo'},{name:'Kazakhstan',code:'kz'},{name:'Kenya',code:'ke'},{name:'Kiribati',code:'ki'},{name:'Kosovo',code:'xk'},
                        {name:'Kuwait',code:'kw'},{name:'Kyrgyzstan',code:'kg'},{name:'Laos',code:'la'},{name:'Latvia',code:'lv'},{name:'Lebanon',code:'lb'},{name:'Lesotho',code:'ls'},{name:'Liberia',code:'lr'},{name:'Libya',code:'ly'},{name:'Liechtenstein',code:'li'},{name:'Lithuania',code:'lt'},
                        {name:'Luxembourg',code:'lu'},{name:'Madagascar',code:'mg'},{name:'Malawi',code:'mw'},{name:'Malaysia',code:'my'},{name:'Maldives',code:'mv'},{name:'Mali',code:'ml'},{name:'Malta',code:'mt'},{name:'Marshall Islands',code:'mh'},{name:'Mauritania',code:'mr'},{name:'Mauritius',code:'mu'},
                        {name:'Mexico',code:'mx'},{name:'Micronesia',code:'fm'},{name:'Moldova',code:'md'},{name:'Monaco',code:'mc'},{name:'Mongolia',code:'mn'},{name:'Montenegro',code:'me'},{name:'Morocco',code:'ma'},{name:'Mozambique',code:'mz'},{name:'Myanmar',code:'mm'},{name:'Namibia',code:'na'},
                        {name:'Nauru',code:'nr'},{name:'Nepal',code:'np'},{name:'Netherlands',code:'nl'},{name:'New Zealand',code:'nz'},{name:'Nicaragua',code:'ni'},{name:'Niger',code:'ne'},{name:'Nigeria',code:'ng'},{name:'North Korea',code:'kp'},{name:'North Macedonia',code:'mk'},{name:'Norway',code:'no'},
                        {name:'Oman',code:'om'},{name:'Pakistan',code:'pk'},{name:'Palau',code:'pw'},{name:'Palestine',code:'ps'},{name:'Panama',code:'pa'},{name:'Papua New Guinea',code:'pg'},{name:'Paraguay',code:'py'},{name:'Peru',code:'pe'},{name:'Philippines',code:'ph'},{name:'Poland',code:'pl'},
                        {name:'Portugal',code:'pt'},{name:'Qatar',code:'qa'},{name:'Romania',code:'ro'},{name:'Russia',code:'ru'},{name:'Rwanda',code:'rw'},{name:'Saint Kitts and Nevis',code:'kn'},{name:'Saint Lucia',code:'lc'},{name:'Saint Vincent and the Grenadines',code:'vc'},{name:'Samoa',code:'ws'},{name:'San Marino',code:'sm'},
                        {name:'Sao Tome and Principe',code:'st'},{name:'Saudi Arabia',code:'sa'},{name:'Senegal',code:'sn'},{name:'Serbia',code:'rs'},{name:'Seychelles',code:'sc'},{name:'Sierra Leone',code:'sl'},{name:'Singapore',code:'sg'},{name:'Slovakia',code:'sk'},{name:'Slovenia',code:'si'},{name:'Solomon Islands',code:'sb'},
                        {name:'Somalia',code:'so'},{name:'South Africa',code:'za'},{name:'South Korea',code:'kr'},{name:'South Sudan',code:'ss'},{name:'Spain',code:'es'},{name:'Sri Lanka',code:'lk'},{name:'Sudan',code:'sd'},{name:'Suriname',code:'sr'},{name:'Sweden',code:'se'},{name:'Switzerland',code:'ch'},
                        {name:'Syria',code:'sy'},{name:'Taiwan',code:'tw'},{name:'Tajikistan',code:'tj'},{name:'Tanzania',code:'tz'},{name:'Thailand',code:'th'},{name:'Timor-Leste',code:'tl'},{name:'Togo',code:'tg'},{name:'Tonga',code:'to'},{name:'Trinidad and Tobago',code:'tt'},{name:'Tunisia',code:'tn'},
                        {name:'Turkey',code:'tr'},{name:'Turkmenistan',code:'tm'},{name:'Tuvalu',code:'tv'},{name:'Uganda',code:'ug'},{name:'Ukraine',code:'ua'},{name:'United Arab Emirates',code:'ae'},{name:'United Kingdom',code:'gb'},{name:'United States',code:'us'},{name:'Uruguay',code:'uy'},{name:'Uzbekistan',code:'uz'},
                        {name:'Vanuatu',code:'vu'},{name:'Vatican City',code:'va'},{name:'Venezuela',code:'ve'},{name:'Vietnam',code:'vn'},{name:'Yemen',code:'ye'},{name:'Zambia',code:'zm'},{name:'Zimbabwe',code:'zw'}
                    ],
                    flagUrl(code) {
                        return 'https://flagcdn.com/20x15/' + code + '.png';
                    },
                    get filtered() {
                        if (!this.search) return this.countries;
                        const s = this.search.toLowerCase();
                        return this.countries.filter(c => c.name.toLowerCase().includes(s));
                    },
                    getCode(name) {
                        const c = this.countries.find(c => c.name === name);
                        return c ? c.code : null;
                    },
                    selectCountry(c) {
                        this.selected = c.name;
                        this.search = '';
                        this.open = false;
                    },
                    clear() {
                        this.selected = null;
                        this.search = '';
                    }
                }" x-init="$watch('open', v => { if(v) $nextTick(() => $refs.searchInput.focus()) })" class="relative">
                    <label class="ui-label">{{ __('account.nationality') }}</label>

                    {{-- Selected / Trigger --}}
                    <button type="button" @click="open = !open" class="ui-input w-full text-left flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 truncate" x-show="selected">
                            <img :src="flagUrl(getCode(selected))" :alt="selected" class="w-5 h-auto rounded-sm shadow-sm" x-show="getCode(selected)" />
                            <span x-text="selected"></span>
                        </span>
                        <span class="text-slate-400" x-show="!selected">{{ __('account.select_country') }}</span>
                        <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-transition.opacity @click.outside="open = false" class="absolute z-50 mt-1 w-full rounded-xl border border-sky-100 bg-white shadow-lg">
                        <div class="p-2 border-b border-slate-100 flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <input x-ref="searchInput" type="text" x-model="search" placeholder="{{ __('account.search_country') }}" class="w-full border-0 p-0 text-sm focus:ring-0 focus:outline-none" />
                            <button type="button" x-show="selected" @click="clear()" class="text-slate-400 hover:text-rose-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <ul class="max-h-60 overflow-y-auto py-1">
                            <template x-for="c in filtered" :key="c.code">
                                <li>
                                    <button type="button" @click="selectCountry(c)" class="w-full text-left px-3 py-2 text-sm flex items-center gap-2.5 hover:bg-sky-50 transition-colors" :class="selected === c.name ? 'bg-sky-50 font-medium text-sky-700' : 'text-slate-700'">
                                        <img :src="flagUrl(c.code)" :alt="c.name" class="w-5 h-auto rounded-sm shadow-sm" />
                                        <span x-text="c.name"></span>
                                        <svg x-show="selected === c.name" class="ml-auto h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </button>
                                </li>
                            </template>
                            <li x-show="filtered.length === 0" class="px-3 py-4 text-sm text-slate-400 text-center">{{ __('account.no_country_found') }}</li>
                        </ul>
                    </div>
                    @error('nationality') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="ui-btn-primary">{{ __('account.save') }}</button>
                <a href="{{ route('wisatawan.password') }}" class="ui-btn-secondary">{{ __('account.change_password') }}</a>
            </div>
        </form>
    </div>
</section>
