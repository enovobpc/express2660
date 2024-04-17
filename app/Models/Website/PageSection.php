<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use App, Auth;

class PageSection extends \App\Models\BaseModel implements Sortable
{
    use SoftDeletes,
        SortableTrait;

    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_website';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages_sections';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id', 'layout', 'container', 'background', 'margin_top', 'margin_bottom',
        'padding_top', 'padding_bottom', 'block_spacer','sort', 'is_published'
    ];

    /**
     * Validator rules
     *
     * @var array
     */
    protected $rules = [
        'page_id' => 'required',
        'layout'  => 'required',
    ];


    /**
     * Default sort column
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'sort'
    ];

    /**
     * Load content inside the template
     *
     * @param $layout
     * @param $sectionId
     * @return mixed|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public static function loadContent ($pageId, $layout, $sectionId, $editMode = false, $blockSpacer = null){

        $layoutHtml   = trans('admin/pages.layouts-templates.'.$layout.'.html');
        $layoutBlocks = (int) trans('admin/pages.layouts-templates.'.$layout.'.blocks');

        $layoutHtml = str_replace('{ROW_SPACER}', ' '.$blockSpacer, $layoutHtml);

        $contents = PageSectionContent::withTranslation()
                            ->where('page_section_id', $sectionId)
                            ->get();

        $contents = $contents->groupBy('block');


        if(!empty($contents)) {
            foreach($contents as $block => $content) {
                $content = $content[0];

                if($content->content_type == 'image') {
                    $blockContent = self::contentImage($content);
                } elseif($content->content_type == 'text') {
                    $blockContent = self::contentText($content);
                } elseif($content->content_type == 'html') {
                    $blockContent = $content->content;
                } elseif($content->content_type == 'video') {
                    $blockContent = self::contentYoutubeVideo($content);
                } elseif($content->content_type == 'thumbnail') {
                    $blockContent = self::contentThumbnail($content);
                } elseif($content->content_type == 'slider_products') {
                    $blockContent = self::contentSliderProducts($content, $editMode);
                } else {
                    $blockContent = self::contentText($content);
                }

                if($editMode) {
                    $blockContent = self::editableContent($pageId, $blockContent, $content);
                }

                $layoutHtml =  str_replace('{'.$block.'}', $blockContent, $layoutHtml);
            }
        }

        for($i = 1 ; $i <= $layoutBlocks ; $i++) {
            $blockId = 'BLOCK_' . $i;

            $blockContent = '';
            $content = new PageSectionContent();
            $content->block = $blockId;
            $content->content_type = 'text';
            $content->page_section_id = $sectionId;

            if($editMode) {
                $blockContent = self::editableContent($pageId, $blockContent, $content);
            }

            $layoutHtml = str_replace('{' . $blockId . '}', $blockContent, $layoutHtml);
        }

        return $layoutHtml;
    }


    /**
     * Make content editable
     *
     * @param $content
     * @return string
     */
    public static function editableContent($pageId, $blockContent, $content = null) {
        return view('admin.website.pages.partials.block', compact('blockContent', 'content', 'blockId', 'pageId'))->render();
    }

    /**
     * Insert content image
     *
     * @param $content
     * @return string
     */
    public static function contentImage($content) {
        return view('admin.website.pages.partials.image', compact('content'))->render();
    }

    /**
     * Insert content youtube video
     *
     * @param $content
     * @return string
     */
    public static function contentYoutubeVideo($content) {
        return view('admin.website.pages.partials.youtube_video', compact('content'))->render();
    }

    /**
     * Insert content thumbnail
     *
     * @param $content
     * @return string
     */
    public static function contentThumbnail($content) {
        return view('admin.website.pages.partials.thumbnail', compact('content'))->render();
    }

    /**
     * Make content editable
     *
     * @param $content
     * @return string
     */
    public static function contentSliderProducts($content, $editMode) {

        if($editMode) {
            return '<h4 class="text-muted text-center margin-top-20"><i class="fa fa-photo"></i> SLIDER DE PRODUTOS</h4>';
        }

        $auth = Auth::guard('customer');

        $wishlist = Wishlist::where('customer_id', @$auth->user()->id)->pluck('product_id')->toArray();

        if($content->include_view == 'products.sliders.new_products') {
            $newProducts = Product::sliderNewProducts();
        } elseif($content->include_view == 'products.sliders.promo_products') {
            $promoProducts = Product::sliderPromoProducts();
        } else {
            return false;
        }

        return view($content->include_view, compact('content', 'newProducts', 'promoProducts', 'wishlist'))->render();
    }

    /**
     * Insert content text
     *
     * @param $content
     * @return string
     */
    public static function contentText($content) {
        $html = '<h2>'.$content->title.'</h2>';
        $html.= $content->content;
        return $html;
    }


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     |
     | Define current model relationships
     */
    public function page()
    {
        return $this->belongsTo('App\Models\Website\Page', 'page_id');
    }

    public function contents()
    {
        return $this->hasMany('App\Models\Website\PageSectionContent', 'page_section_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |--------------------------------------------------------------------------
     |
     | Scopes allow you to easily re-use query logic in your models.
     | To define a scope, simply prefix a model method with scope.
     |
    */

    public function scopePublished($query, $published = true) {

        if(!Auth::check()) {
            return $query->where('is_published', $published);
        }

        return $query;
    }

    /*
      |--------------------------------------------------------------------------
      | Accessors & Mutators
      |--------------------------------------------------------------------------
      |
      | Eloquent provides a convenient way to transform your model attributes when
      | getting or setting them. Simply define a 'getFooAttribute' method on your model
      | to declare an accessor. Keep in mind that the methods should follow camel-casing,
      | even though your database columns are snake-case.
      |
     */
}
