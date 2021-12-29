<?php

namespace App;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\Debugbar\Facade as Debug;

class Post extends Model
{
    use SoftDeletes;

    protected $casts = [
        'wp_json' => 'json',
    ];

    private $cardImageSettings = [
        "h" => 200,
        "w" => 200 * 3.2361/2,
        "fit" => "crop",
        "crop" => "focalpoint",
        "auto" => "format,compress,enhance"
    ];

    private $bannerImageSettings = [
        "h" => 200,
        "w" => 200 * 30/9,
        "fit" => "crop",
        "crop" => "focalpoint",
        "auto" => "format,compress,enhance"
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = Str::random(5);
        });

        static::saving(function ($model) {
            $model->user_id = Auth::id();

            if (!empty($model->wp_url)) {
                $client = new Client();
                $res = $client->get($model->wp_url);
                $data = collect(json_decode($res->getBody()->getContents()));
//                Storage::disk('local')->put('debug.txt', 'hello world content');
//                debug($data);
                $output = [
                    "title" => $data['title']->rendered,
                    "subtitle" => $data['yoast_head_json']->og_description,
                    "image" => $data['yoast_head_json']->og_image[0]->url,
                    "description" => strip_tags($data['excerpt']->rendered),
                    "html" => $data['content']->rendered
                ];
                debug($output);

                $model->title = html_entity_decode($data['title']->rendered);
                $model->subtitle = $data['yoast_head_json']->og_description;
                $model->image = $data['yoast_head_json']->og_image[0]->url;
                $model->description = html_entity_decode(strip_tags($data['excerpt']->rendered));
                $model->html = $data['content']->rendered;
                $model->markdown = strip_tags($model->html);
                $model->wp_json = $data;
//                dd($data);
            } else {
                $model->html = Markdown::parse($model->markdown);
            }

            $model->tags = implode(",", $model->getKeywords());
        });

//        static::updating(function ($model) {
//            $model->html = Markdown::parse($model->markdown);
//            $model->tags = implode(",", $model->getKeywords());
//        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function findByUid ($uid) {
        return static::where('uid', $uid)->firstOrFail();
    }

    public function getRouteKeyName()
    {
        return 'uid';
    }

    public function getKeywords() {
        return array_map(function ($tag) {
            return Str::slug($tag);
        }, explode(",", $this->tags));
    }

    public function getSlugAttribute() {
        return Str::slug($this->title);
    }

    public function getUrlAttribute() {
        return route('blog.reader', [
            "post"=>$this,
            "slug" => $this->slug
        ]);
    }

    public function getPictureAttribute () {
        return imgixUrl($this->image, $this->cardImageSettings);
    }

    public function getScaledPicture($dpr) {
        return imgixUrl($this->image, array_merge(
            $this->cardImageSettings,
            ["dpr" => $dpr]
        ));
    }


    public function getScaledImages($amount=3) {
        $srcset = [];
        for ($i = 1; $i <= $amount; $i++) {
            $srcset[] = $this->getScaledPicture($i);
        }
        return $srcset;
    }

    public function getPictureSrcsetAttribute($amount=3) {
        $images = $this->getScaledImages($amount);
        return implode(",", array_map(function ($image, $index) {
            return $image . " $index" . "x";
        }, $images, array_keys($images)));
    }

    public function getBannerAttribute () {
        return imgixUrl($this->image, $this->bannerImageSettings);
    }

    public function getScaledBanner($dpr) {
        return imgixUrl($this->image, array_merge(
            $this->bannerImageSettings,
            ["dpr" => $dpr]
        ));
    }

    public function getBannerSrcsetAttribute($amount=3) {
        $srcset = [];
        for ($i = 1; $i <= $amount; $i++) {
            $srcset[] = $this->getScaledBanner($i) . " " . $i . "x";
        }
        return implode(",", $srcset);
    }
}