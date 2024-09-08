<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category as CategoryModel;

class Category extends Component
{
    public $categories, $name, $description, $category_id;
    public $updateCategory = false;
    protected $listeners = [
        'deleteCategory'=>'destroy'
    ];
    // Validation Rules
    protected $rules = [
        'name'=>'required',
        'description'=>'required'
    ];
    public function render()
    {
        $this->categories = CategoryModel::select('id','name','description')->get();
        return view('livewire.category');
    }

    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
    }

    public function store()
    {
        // Validate Form Request
        $this->validate();
        try {
            // Create Category
            CategoryModel::create([
                'name'=>$this->name,
                'description'=>$this->description
            ]);

            // Set Flash Message
            session()->flash('success','Category Created Successfully!!');
            // Reset Form Fields After Creating Category
            $this->resetFields();
        } catch(\Exception $e) {
            // Set Flash Message
            session()->flash('error',$e->getMessage());
            // Reset Form Fields After Creating Category
            $this->resetFields();
        }
    }

    public function cancel()
    {
        $this->updateCategory = false;
        $this->resetFields();
    }

    public function edit($id)
    {
        $category = CategoryModel::findOrFail($id);
        $this->name = $category->name;
        $this->description = $category->description;
        $this->category_id = $category->id;
        $this->updateCategory = true;
    }

    public function update()
    {
        // Validate request
        $this->validate();
        try {
            // Update category
            CategoryModel::find($this->category_id)->fill([
                'name'=>$this->name,
                'description'=>$this->description
            ])->save();
            session()->flash('success','Category Updated Successfully!!');

            $this->cancel();
        } catch(\Exception $e) {
            session()->flash('error','Something goes wrong while updating category!!');
            $this->cancel();
        }
    }
    public function destroy($id)
    {
        try {
            CategoryModel::find($id)->delete();
            session()->flash('success',"Category Deleted Successfully!!");
        } catch(\Exception $e) {
            session()->flash('error',"Something goes wrong while deleting category!!");
        }
    }
}
