<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public array $form_rules = [
        'form_type' => 'required|in_list[url,qr,note]',
    ];

    public array $form_rules_errors = [
        'form_type' => [
            'required' => 'Form unknown.',
            'in_list' => 'Form unknown.',
        ],
    ];

    private array $customize_rules = [
        'shortcode' => 'permit_empty|alpha_dash|max_length[50]|min_length[4]|is_unique[links.shortcode]',
        'password' => 'permit_empty|min_length[4]|max_length[150]',
        'expiration_type' => 'permit_empty|in_list[time,visits]',
        'expiration_after' => 'permit_empty|integer',
        'expiration_unit' => 'permit_empty|in_list[minutes,hours,days,weeks,months]',
        'expiration_visits' => 'permit_empty|integer',
    ];

    public array $url_rules = [];

    public array $url_rules_errors = [
        'shortcode' => [
            'is_unique' => 'The shortcode already exists. Try another one.',
        ],
    ];

    public array $note_rules = [];

    public array $note_rules_errors = [
        'shortcode' => [
            'is_unique' => 'The shortcode already exists. Try another one.',
        ],
    ];


    public function __construct()
    {
        parent::__construct();

        $this->url_rules = array_merge(
            [
                'url' => 'required|valid_url_strict[http,https]',
            ],
            $this->customize_rules
        );

        $this->note_rules = array_merge(
            [
                'content' => 'required',
                'note_type' => 'required|in_list[abap,ada,assembly_x86,c_cpp,clojure,cobol,csharp,css,dart,erlang,fortran,golang,groovy,haskell,html,java,javascript,json,kotlin,lua,matlab,objectivec,other,pascal,perl,php,powershell,python,r,rich_text,ruby,rust,scala,swift,typescript,xml]',
            ],
            $this->customize_rules
        );
    }
}
