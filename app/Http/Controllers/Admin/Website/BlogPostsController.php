<?php

namespace App\Http\Controllers\Admin\Website;

use App\Models\Website\BlogPost;
use App\Models\Website\BlogPostImage;
use App\Models\Website\BlogPostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Facades\Datatables;
use File, Croppa, Response;

class BlogPostsController extends \App\Http\Controllers\Admin\Controller {

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = 'blog';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',blog']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return $this->setContent('admin.website.blog.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        $action = 'Nova Publicação';
        
        $post = new BlogPost();
                
        $formOptions = array('route' => array('admin.website.blog.posts.store'), 'method' => 'POST', 'files' => true);
 
        return $this->setContent('admin.website.blog.edit', compact('post', 'action', 'formOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id) {
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
        $action = 'Editar Notícia';
        
        $post = BlogPost::findOrfail($id);

        $formOptions = array('route' => array('admin.website.blog.posts.update', $post->id), 'method' => 'PUT', 'files' => true);

        $images = [];

        foreach ($post->images as $image) {
            $images[] = array(
                'name'  => $image->filename,
                'size'  => @filesize(@$image->filepath),
                'id'    => $image->id,
                'type'  => 'image/jpg',
                'file'  => asset($image->getThumb())
            );
        }
        
        return $this->setContent('admin.website.blog.edit', compact('post', 'images', 'action', 'formOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        
        $input = $request->all();
        $input['is_highlight'] = $request->get('highlight', false);
        $input['is_published'] = $request->get('published', false);
        $input['tags'] = explode(',', $request->get('tags'));
        $deleted = json_decode(@$input['deleted_images']);
        
        $post = BlogPost::findOrNew($id);

        if ($post->validate($input)) {
            $post->fill($input);
            
            //delete image
            if ($request->delete_photo && !empty($post->filepath)) {
                if(File::exists(public_path($post->filepath))) {
                    Croppa::delete(public_path($post->filepath));
                }
                $post->filepath = null;
                $post->filename = null;
            }
            
            //upload image
            if($request->hasFile('image')) {

                if ($post->exists && !empty($post->filepath)) {
                    Croppa::delete(public_path($post->filepath));
                }

                if (!$post->upload($request->file('image'))) {
                    return Redirect::back()->withInput()->with('error', 'Não foi possível alterar a imagem.');
                }
                
            } else {
                $post->save();
            }


            //assign tags
            $post->tags()->forceDelete();

            foreach ($input['tags'] as $tag) {
                $postTag = new BlogPostTag();
                $postTag->blog_post_id = $post->id;
                $postTag->tag  = trim($tag);
                $postTag->slug = str_slug($tag);
                $postTag->save();
            }
            
            //remove images if deleted
            if (!empty($deleted)) {
                $deleteImages = BlogPostImage::whereIn('id', $deleted)->get();
             
                foreach ($deleteImages as $image) {
                    if(File::exists(public_path($image->filepath))) {
                        Croppa::delete(public_path($image->filepath));
                    }
                    $image->delete();
                }
            }

            //exists new attachments
            if ($request->hasFile('images')) {
                foreach ($input['images'] as $image) {
                    //validate file
                    if (!is_null($image) && $image->isValid()) {
                        $newImage = new BlogPostImage();
                        $newImage->blog_post_id = $post->id;
                        $newImage->upload($image);
                    }
                }
            }

            return Redirect::route('admin.website.blog.posts.edit', $post->id)->with('success', 'Dados gravados com sucesso.');
        }
        
        return Redirect::back()->withInput()->with('error', $post->errors()->first());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        
        $result = BlogPost::destroy($id);

        if (!$result) {
            return Redirect::back()->with('error', 'Ocorreu um erro ao tentar remover a notícia.');
        }

        return Redirect::route('admin.website.blog.index')->with('success', 'Notícia removida com sucesso.');
    }
    
    /**
     * Remove all selected resources from storage.
     * GET /admin/brands/selected/destroy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request) {
        
        $ids = explode(',', $request->ids);
        
        $result = BlogPost::whereIn('id', $ids)->delete();
        
        if (!$result) {
            return Redirect::back()->with('error', 'Não foi possível remover os registos selecionados');
        }

        return Redirect::back()->with('success', 'Registos selecionados removidos com sucesso.');
    }
    
    /**
     * Loading table data
     * 
     * @return Datatables
     */
    public function datatable(Request $request) {

        $data = BlogPost::withTranslation()->select();

        return Datatables::of($data)
            ->add_column('photo', function($row) {
                return view('admin.partials.datatables.photo', compact('row'))->render();
            })
            ->edit_column('date', function($row) {
                return $row->date->format('Y-m-d');
            })
            ->edit_column('title', function($row) {
                return view('admin.website.blog.datatables.title', compact('row'))->render();
            })
            ->edit_column('is_highlight', function($row) {
                return view('admin.website.blog.datatables.highlight', compact('row'))->render();
            })
            ->edit_column('is_published', function($row) {
                return view('admin.website.blog.datatables.published', compact('row'))->render();
            })
            ->add_column('share', function($row) {
                return view('admin.website.blog.datatables.share', compact('row'))->render();
            })
            ->add_column('select', function($row) {
                return view('admin.partials.datatables.select', compact('row'))->render();
            })
            ->edit_column('created_at', function($row) {
                return view('admin.partials.datatables.created_at', compact('row'))->render();
            })
            ->add_column('actions', function($row) {
                return view('admin.website.blog.datatables.actions', compact('row'))->render();
            })
            ->make(true);
    }
}
