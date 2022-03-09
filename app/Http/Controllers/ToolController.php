<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessBase64ConverterRequest;
use App\Http\Requests\ProcessCaseConverterRequest;
use App\Http\Requests\ProcessCssMinifierRequest;
use App\Http\Requests\ProcessDnsLookupRequest;
use App\Http\Requests\ProcessHtmlMinifierRequest;
use App\Http\Requests\ProcessIndexedPagesCheckerRequest;
use App\Http\Requests\ProcessIpLookupRequest;
use App\Http\Requests\ProcessJsMinifierRequest;
use App\Http\Requests\ProcessKeywordGeneratorRequest;
use App\Http\Requests\ProcessLoremIpsumGeneratorRequest;
use App\Http\Requests\ProcessMd5GeneratorRequest;
use App\Http\Requests\ProcessPasswordGeneratorRequest;
use App\Http\Requests\ProcessQrGeneratorRequest;
use App\Http\Requests\ProcessSerpCheckerRequest;
use App\Http\Requests\ProcessSslCheckerRequest;
use App\Http\Requests\ProcessTextToSlugRequest;
use App\Http\Requests\ProcessUrlConverterRequest;
use App\Http\Requests\ProcessUserAgentParserRequest;
use App\Http\Requests\ProcessWebsiteStatusCheckerRequest;
use App\Http\Requests\ProcessWhoisLookupRequest;
use App\Http\Requests\ProcessWordCounterRequest;
use Faker\Provider\Lorem;
use GeoIp2\Database\Reader as GeoIP;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\TransferStats;
use hexydec\css\cssdoc;
use hexydec\html\htmldoc;
use hexydec\jslite\jslite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Iodev\Whois\Factory as Whois;
use Spatie\SslCertificate\SslCertificate;
use WhichBrowser\Parser as UserAgent;

class ToolController extends Controller
{
    /**
     * List the Projects.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('tools.list', []);
    }

    /**
     * Show the Text to Slug form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function textToSlug(Request $request)
    {
        return view('tools.content', ['view' => 'text-to-slug']);
    }

    /**
     * Process the Text to Slug.
     *
     * @param ProcessTextToSlugRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processTextToSlug(ProcessTextToSlugRequest $request)
    {
        return view('tools.content', ['view' => 'text-to-slug', 'content' => $request->input('content'), 'result' => Str::slug($request->input('content'))]);
    }

    /**
     * Show the Case Converter form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function caseConverter(Request $request)
    {
        return view('tools.content', ['view' => 'case-converter']);
    }

    /**
     * Process the Case Converter.
     *
     * @param ProcessCaseConverterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processCaseConverter(ProcessCaseConverterRequest $request)
    {
        $method = $request->input('type');

        return view('tools.content', ['view' => 'case-converter', 'content' => $request->input('content'), 'type' => $request->input('type'), 'result' => Str::$method($request->input('content'))]);
    }

    /**
     * Show the Word Counter form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wordCounter(Request $request)
    {
        return view('tools.content', ['view' => 'word-counter']);
    }

    /**
     * Process the Word Counter.
     *
     * @param ProcessWordCounterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processWordCounter(ProcessWordCounterRequest $request)
    {
        $wordCount = str_word_count($request->input('content'));
        $letterCount = mb_strlen($request->input('content'));

        return view('tools.content', ['view' => 'word-counter', 'content' => $request->input('content'), 'wordCount' => $wordCount, 'letterCount' => $letterCount]);
    }

    /**
     * Show the Lorem Ipsum Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loremIpsumGenerator(Request $request)
    {
        return view('tools.content', ['view' => 'lorem-ipsum-generator']);
    }

    /**
     * Process the Lorem Ipsum Generator.
     *
     * @param ProcessLoremIpsumGeneratorRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processLoremIpsumGenerator(ProcessLoremIpsumGeneratorRequest $request)
    {
        $method = $request->input('type');

        return view('tools.content', ['view' => 'lorem-ipsum-generator', 'type' => $request->input('type'), 'number' => $request->input('number'), 'results' => Lorem::$method($request->input('number'))]);
    }

    /**
     * Show the MD5 Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function md5Generator(Request $request)
    {
        return view('tools.content', ['view' => 'md5-generator']);
    }

    /**
     * Process the MD5 Generator.
     *
     * @param ProcessMd5GeneratorRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processMd5Generator(ProcessMd5GeneratorRequest $request)
    {
        return view('tools.content', ['view' => 'md5-generator', 'content' => $request->input('content'), 'result' => md5($request->input('content'))]);
    }

    /**
     * Show the User-Agent parser form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userAgentParser(Request $request)
    {
        return view('tools.content', ['view' => 'user-agent-parser']);
    }

    /**
     * Process the User-Agent parser.
     *
     * @param ProcessUserAgentParserRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processUserAgentParser(ProcessUserAgentParserRequest $request)
    {
        return view('tools.content', ['view' => 'user-agent-parser', 'content' => $request->input('content'), 'userAgent' => $request->input('user_agent'), 'result' => new UserAgent($request->input('user_agent'))]);
    }

    /**
     * Show the IP Lookup form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ipLookup(Request $request)
    {
        return view('tools.content', ['view' => 'ip-lookup']);
    }

    /**
     * Process the IP Lookup.
     *
     * @param ProcessIpLookupRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processIpLookup(ProcessIpLookupRequest $request)
    {
        // Get the user's geolocation
        try {
            $result = (new GeoIP(storage_path('app/geoip/GeoLite2-City.mmdb')))->city($request->input('ip'))->raw;
        } catch (\Exception $e) {
            $result = false;
        }

        return view('tools.content', ['view' => 'ip-lookup', 'content' => $request->input('content'), 'result' => $result]);
    }

    /**
     * Show the Password Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function passwordGenerator(Request $request)
    {
        return view('tools.content', ['view' => 'password-generator']);
    }

    /**
     * Process the Password Generator.
     *
     * @param ProcessPasswordGeneratorRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processPasswordGenerator(ProcessPasswordGeneratorRequest $request)
    {
        $length = $request->input('length');

        $characters = [];
        if ($request->input('upper_case')) {
            $characters[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($request->input('lower_case')) {
            $characters[] = 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($request->input('digits')) {
            $characters[] = '1234567890';
        }
        if ($request->input('symbols')) {
            $characters[] = '!@#$%&*?';
        }

        $all = $password = '';

        foreach ($characters as $character) {
            $password .= $character[array_rand(str_split($character))];
            $all .= $character;
        }

        $all = str_split($all);

        for ($i = 0; $i < $length - count($characters); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return view('tools.content', ['view' => 'password-generator', 'content' => $request->input('content'), 'lowerCase' => (bool)$request->input('lower_case'), 'upperCase' => (bool) $request->input('upper_case'), 'digits' => (bool) $request->input('digits'), 'symbols' => (bool) $request->input('symbols'), 'result' => $password, 'length' => $length]);
    }

    /**
     * Show the QR Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrGenerator(Request $request)
    {
        return view('tools.content', ['view' => 'qr-generator']);
    }

    /**
     * Process the QR Generator.
     *
     * @param ProcessQrGeneratorRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processQrGenerator(ProcessQrGeneratorRequest $request)
    {
        return view('tools.content', ['view' => 'qr-generator', 'content' => $request->input('content'), 'size' => $request->input('size'), 'result' => $request->input('content')]);
    }

    /**
     * Show the URL Converter form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function urlConverter(Request $request)
    {
        return view('tools.content', ['view' => 'url-converter']);
    }

    /**
     * Process the URL Converter.
     *
     * @param ProcessUrlConverterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processUrlConverter(ProcessUrlConverterRequest $request)
    {
        if ($request->input('type') == 'encode') {
            $result = urlencode($request->input('content'));
        } else {
            $result = urldecode($request->input('content'));
        }

        return view('tools.content', ['view' => 'url-converter', 'content' => $request->input('content'), 'type' => $request->input('type'), 'result' => $result]);
    }

    /**
     * Show the Base64 Converter form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function base64Converter(Request $request)
    {
        return view('tools.content', ['view' => 'base64-converter']);
    }

    /**
     * Process the Base64 Converter.
     *
     * @param ProcessBase64ConverterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processBase64Converter(ProcessBase64ConverterRequest $request)
    {
        if ($request->input('type') == 'encode') {
            $result = base64_encode($request->input('content'));
        } else {
            $result = base64_decode($request->input('content'));
        }

        return view('tools.content', ['view' => 'base64-converter', 'content' => $request->input('content'), 'type' => $request->input('type'), 'result' => $result]);
    }

    /**
     * Show the DNS Lookup form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dnsLookup(Request $request)
    {
        return view('tools.content', ['view' => 'dns-lookup']);
    }

    /**
     * Process the DNS Lookup.
     *
     * @param ProcessDnsLookupRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processDnsLookup(ProcessDnsLookupRequest $request)
    {
        $domain = str_replace(['http://', 'https://'], '', $request->input('domain'));

        try {
            $dnsRecords = dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX + DNS_TXT + DNS_NS);
        } catch (\Exception $e) {
            $dnsRecords = [];
        }

        return view('tools.content', ['view' => 'dns-lookup', 'domain' => $domain, 'results' => $dnsRecords]);
    }

    /**
     * Show the WHOIS Lookup form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function whoisLookup(Request $request)
    {
        return view('tools.content', ['view' => 'whois-lookup']);
    }

    /**
     * Process the WHOIS Lookup.
     *
     * @param ProcessWhoisLookupRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processWhoisLookup(ProcessWhoisLookupRequest $request)
    {
        $domain = str_replace(['http://', 'https://', 'www.'], '', $request->input('domain'));

        $whoisRecords = false;
        try {
            $whoisRecords = Whois::get()->createWhois()->loadDomainInfo($domain);
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'whois-lookup', 'domain' => $domain, 'result' => $whoisRecords]);
    }

    /**
     * Show the Website Status Checker form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function websiteStatusChecker(Request $request)
    {
        return view('tools.content', ['view' => 'website-status-checker']);
    }

    /**
     * Process the Website Status Checker.
     *
     * @param ProcessWebsiteStatusCheckerRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processWebsiteStatusChecker(ProcessWebsiteStatusCheckerRequest $request)
    {
        $domain = str_replace(['http://', 'https://'], '', $request->input('domain'));

        $client = new HttpClient();

        $websiteStatusRequest = false;
        $websiteStatusStats = null;
        try {
            $websiteStatusRequest = $client->request('GET', 'http://' . $domain, [
                'proxy' => [
                    'http' => getRequestProxy(),
                    'https' => getRequestProxy()
                ],
                'http_errors' => false,
                'verify' => false,
                'connect_timeout' => config('settings.request_connection_timeout'),
                'allow_redirects' => [
                    'max'             => 10,
                    'strict'          => true,
                    'referer'         => true,
                    'protocols'       => ['http', 'https']
                ],
                'headers' => [
                    'User-Agent' => config('settings.request_user_agent')
                ],
                'on_stats' => function (TransferStats $stats) use (&$websiteStatusStats) {
                    if ($stats->hasResponse()) {
                        $websiteStatusStats = $stats->getHandlerStats();
                    }
                }
            ]);
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'website-status-checker', 'domain' => $domain, 'result' => $websiteStatusRequest, 'stats' => $websiteStatusStats]);
    }

    /**
     * Show the SSL Checker form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sslChecker(Request $request)
    {
        return view('tools.content', ['view' => 'ssl-checker']);
    }

    /**
     * Process the SSL Checker.
     *
     * @param ProcessSslCheckerRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processSslChecker(ProcessSslCheckerRequest $request)
    {
        $domain = str_replace(['http://', 'https://'], '', $request->input('domain'));

        $ssl = false;
        try {
            $ssl = SslCertificate::createForHostName($domain, config('settings.request_connection_timeout'), false);
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'ssl-checker', 'domain' => $domain, 'result' => $ssl]);
    }

    /**
     * Show the Keyword Generator form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function keywordGenerator(Request $request)
    {
        return view('tools.content', ['view' => 'keyword-generator']);
    }

    /**
     * Process the Keyword Generator.
     *
     * @param ProcessKeywordGeneratorRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processKeywordGenerator(ProcessKeywordGeneratorRequest $request)
    {
        $client = new HttpClient();

        $keywords = false;
        try {
            $keywordRequest = $client->request('GET', 'https://suggestqueries.google.com/complete/search?output=firefox&client=firefox&q=' . urlencode($request->input('keyword')), [
                'proxy' => [
                    'http' => getRequestProxy(),
                    'https' => getRequestProxy()
                ],
                'http_errors' => false,
                'connect_timeout' => config('settings.request_connection_timeout'),
                'allow_redirects' => [
                    'max'             => 10,
                    'strict'          => true,
                    'referer'         => true,
                    'protocols'       => ['http', 'https']
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:96.0) Gecko/20100101 Firefox/96.0'
                ]
            ]);

            $result = json_decode($keywordRequest->getBody()->getContents(), true);

            // Get the list of keywords suggestions
            $keywords = $result[1];
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'keyword-generator', 'keyword' => $request->input('keyword'), 'keywords' => $keywords]);
    }

    /**
     * Show the SERP Checker form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function serpChecker(Request $request)
    {
        return view('tools.content', ['view' => 'serp-checker']);
    }

    /**
     * Process the SERP Checker.
     *
     * @param ProcessSerpCheckerRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processSerpChecker(ProcessSerpCheckerRequest $request)
    {
        $client = new HttpClient();

        $results = false;
        try {
            $searchRequest = $client->request('GET', 'https://www.googleapis.com/customsearch/v1?key=' . urlencode(config('settings.gcs_key')) . '&cx=' . config('settings.gcs_id') . '&gl=' . urlencode($request->input('country')) . '&q=' . urlencode($request->input('keyword')), [
                'http_errors' => false,
                'connect_timeout' => config('settings.request_connection_timeout'),
                'allow_redirects' => [
                    'max'             => 10,
                    'strict'          => true,
                    'referer'         => true,
                    'protocols'       => ['http', 'https']
                ],
                'headers' => [
                    'User-Agent' => config('settings.request_user_agent')
                ]
            ]);

            $results = json_decode($searchRequest->getBody()->getContents(), true);
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'serp-checker', 'keyword' => $request->input('keyword'), 'domain' => $request->input('domain'), 'country' => $request->input('country'), 'results' => $results]);
    }

    /**
     * Show the Indexed Pages Checker form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexedPagesChecker(Request $request)
    {
        return view('tools.content', ['view' => 'indexed-pages-checker']);
    }

    /**
     * Process the Indexed Pages Checker.
     *
     * @param ProcessIndexedPagesCheckerRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function processIndexedPagesChecker(ProcessIndexedPagesCheckerRequest $request)
    {
        $client = new HttpClient();

        $result = false;
        try {
            $searchRequest = $client->request('GET', 'https://www.googleapis.com/customsearch/v1?key=' . urlencode(config('settings.gcs_key')) . '&cx=' . config('settings.gcs_id') . '&gl=' . urlencode($request->input('country')) . '&q=' . urlencode('site:' . $request->input('domain')), [
                'http_errors' => false,
                'connect_timeout' => config('settings.request_connection_timeout'),
                'allow_redirects' => [
                    'max'             => 10,
                    'strict'          => true,
                    'referer'         => true,
                    'protocols'       => ['http', 'https']
                ],
                'headers' => [
                    'User-Agent' => config('settings.request_user_agent')
                ]
            ]);

            $result = json_decode($searchRequest->getBody()->getContents(), true);
        } catch (\Exception $e) {}

        return view('tools.content', ['view' => 'indexed-pages-checker', 'domain' => $request->input('domain'), 'country' => $request->input('country'), 'result' => $result]);
    }
    
    /**
     * Show the JS Minifier form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function jsMinifier(Request $request)
    {
        return view('tools.content', ['view' => 'js-minifier']);
    }

    /**
     * Process the JS Minifier.
     *
     * @param ProcessJsMinifierRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processJsMinifier(ProcessJsMinifierRequest $request)
    {
        $js = new jslite();

        $content = $request->input('content');
        if ($js->load($request->input('content'))) {
            $js->minify();
            $content = $js->compile();
        }

        return view('tools.content', ['view' => 'js-minifier', 'content' => $content]);
    }

    /**
     * Show the CSS Minifier form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cssMinifier(Request $request)
    {
        return view('tools.content', ['view' => 'css-minifier']);
    }

    /**
     * Process the CSS Minifier.
     *
     * @param ProcessCssMinifierRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processCssMinifier(ProcessCssMinifierRequest $request)
    {
        $css = new cssdoc();

        $content = $request->input('content');
        if ($css->load($request->input('content'))) {
            $css->minify();
            $content = $css->compile();
        }

        return view('tools.content', ['view' => 'css-minifier', 'content' => $content]);
    }

    /**
     * Show the HTML Minifier form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function htmlMinifier(Request $request)
    {
        return view('tools.content', ['view' => 'html-minifier']);
    }

    /**
     * Process the HTML Minifier.
     *
     * @param ProcessHtmlMinifierRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function processHtmlMinifier(ProcessHtmlMinifierRequest $request)
    {
        $html = new htmldoc();

        $content = $request->input('content');
        if ($html->load($request->input('content'))) {
            $html->minify();
            $content = $html->save();
        }

        return view('tools.content', ['view' => 'html-minifier', 'content' => $content]);
    }
}
