<?php

namespace App\Http\Controllers\Backend;

use App\Core\PrimaryController;
use App\Core\Services\Image\PrimaryImageService;
use App\Src\Category\CategoryRepository;
use App\Http\Requests\Backend\CategoryUpdate;
use App\Http\Requests\Backend\CategoryCreate;
use Illuminate\Support\Facades\Cache;

class CategoryController extends PrimaryController
{
    protected $category;


    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->category->getParentCategoriesWithChildren();

        return view('backend.modules.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.modules.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CategoryCreate $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryCreate $request)
    {
        if ($request->hasFile('image')) {
            $image = new PrimaryImageService();
            $image = $image->CreateImage($request->file('image'),['1','1'],['1','1'],['1000','250']);
            $request->request->add(['image' => $image]);
        }

        $category = $this->category->create($request->request->all());

        if ($category) {

            return redirect()->route('backend.category.index')->with('success', 'successfully created');

        }

        return redirect()->back()->with('error', 'not created !!');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = $this->category->getById($id);

        return view('backend.modules.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryUpdate $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategxoryUpdate $request, $id)
    {
        if ($request->hasFile('image')) {
            $image = new PrimaryImageService();
            $image = $image->CreateImage($request->file('image'),['1','1'],['1','1'],['1000','250']);
            $this->category->getById($id)->update(['image' => $image]);
        }

        if ($this->category->getById($id)->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'description_en' => $request->description_en,
            'description_ar' => $request->description_ar,
            'limited' => $request->limited
        ])
        ) {
            return redirect()->route('backend.category.index')->with('success', 'category updated!!');
        }

        return redirect()->back()->with('error', 'not saved !!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check if category not assigned to any of products
        if ($this->category->getById($id)->products->count() > 0) {
            return redirect()->back()->with('error', 'Category Assigned to Product!!');
        }
        //check if category has subcategories
        if ($this->category->getById($id)->children->count() > 0) {
            return redirect()->back()->with('error', 'Category Already has been Assigned To SubCategory!!');
        }

        if ($this->category->getById($id)->delete()) {

            return redirect()->route('backend.category.index')->with('success', 'Category Removed successfully!');

        }
        return redirect()->back()->with('error', 'not successful !!');
    }
}
