<?php

namespace App\Http\Livewire\Admin\Post;

use alert;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Livewire\WithPagination;
class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $listeners = ['remove'];
        public $showData = true;
        public $createData = false;
        public $updateData = false;

        public $name='';
        public $image;
        public $mnged;

        public $edit_id;
        public $edit_name;
        public $old_image;
        public $new_image;

        protected $rules = [
            'name' => 'required',
            'image' => 'required'
        ];

        public function resetField()
        {
            $this->name = "";
            $this->image = "";
            $this->edit_name = "";
            $this->new_image = "";
            $this->old_image = "";
            $this->edit_id = "";
        }

        public function showForm()
        {
            $this->showData = false;
            $this->createData = true;
        }

        use WithFileUploads;
        public function create()
        {
            $posts = new Post();
            $this->validate([
                'name' => 'required',
                'image' => 'required'
            ]);

            $filename = "";
            if ($this->image) {
                $filename = $this->image->store('posts', 'public');
            } else {
                $filename = Null;
            }

            $posts->name = $this->name;
            $posts->image = $filename;
            $result = $posts->save();
            if ($result) {
                session()->flash('success', 'Add Successfully');
                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'success',
                    'message' => 'User Created Successfully!',
                    'text' => 'It will list on users table soon.'
                ]);
                $this->resetField();
                $this->showData = true;
                $this->createData = false;

            } else {
                session()->flash('error', 'Not Add Successfully');
            }
        }

        public function edit($id)
        {

            $this->showData = false;
            $this->createData = false;

            $this->updateData = true;
            $posts = Post::findOrFail($id);
            $this->edit_id = $posts->id;
            $this->edit_name = $posts->name;
            $this->old_image = $posts->image;
        }

        public function update($id)
        {
            $posts =Post::findOrFail($id);
            $this->validate([
                'edit_name' => 'required',
            ]);

            $filename = "";
            $destination=public_path('storage\\'.$posts->image);
            if ($this->new_image != null) {
                if(File::exists($destination)){
                    File::delete($destination);
                }
                $filename = $this->new_image->store('posts', 'public');
            } else {
                $filename = $this->old_image;
            }

            $posts->name = $this->edit_name;
            $posts->image = $filename;
            $result = $posts->save();
            if ($result) {
                session()->flash('success', 'Update Successfully');
                $this->resetField();
                $this->showData = true;
                $this->updateData = false;

            } else {
                session()->flash('error', 'Not Update Successfully');
            }
        }

        public function delete($id)
        {


            $posts=Post::findOrFail($id);
            $destination=public_path('storage\\'.$posts->image);
            if(File::exists($destination)){

                File::delete($destination);
            }

            $result=$posts->delete();

            if ($result) {
                session()->flash('success', 'Delete Successfully');
                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'success',
                    'message' => 'User Created Successfully!',
                    'text' => 'It will list on users table soon.'
                ]);

            } else {
                session()->flash('error', 'Not Delete Successfully');
            }

        }





    public function render()
    {
        $posts = Post::orderBy('id', 'DESC')->get();
        return view('livewire.admin.post.index', ['posts' => $posts])->layout('layouts.app');
    }
}