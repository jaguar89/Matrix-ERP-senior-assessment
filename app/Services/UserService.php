<?php

namespace App\Services;

use App\enums\PrefixName;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;

class UserService implements UserServiceInterface
{
    /**
     * The model instance.
     *
     * @var App\User
     */
    protected $model;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \App\User $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(User $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model.
     *
     * @param int $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'prefixname' => ['required', Rule::in(array_column(PrefixName::cases(), 'value'))],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => $id ? ['nullable', 'string', 'confirmed', Password::defaults()] : ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        return $this->model->latest()->paginate(10);
    }

    /**
     * Create model resource.
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        $validated = Validator::make($attributes, $this->rules())->validate();

        $validated['password'] = $this->hash($validated['password']);

        if (isset($attributes['photo']) && $attributes['photo'] instanceof UploadedFile) {
            $validated['photo'] = $this->upload($attributes['photo']);
        }

        return $this->model->create($validated);
    }

    /**
     * Retrieve model resource details.
     * Abort to 404 if not found.
     *
     * @param integer $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Update model resource.
     *
     * @param integer $id
     * @param array $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $validated = Validator::make($attributes, $this->rules($id))->validate();
        $user = $this->model->findOrFail($id);

        if (isset($validated['password'])) {
            $validated['password'] = $this->hash($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($attributes['photo']) && $attributes['photo'] instanceof UploadedFile) {
            $validated['photo'] = $this->upload($attributes['photo']);
        }

        return $user->update($validated);
    }

    /**
     * Soft delete model resource.
     *
     * @param integer|array $id
     * @return void
     */
    public function destroy($id)
    {
        if (is_array($id)) {
            $this->model->whereIn('id', $id)->delete();
        } else {
            $this->model->findOrFail($id)->delete();
        }
    }

    /**
     * Include only soft deleted records in the results.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listTrashed()
    {
        return $this->model->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
    }

    /**
     * Restore model resource.
     *
     * @param integer|array $id
     * @return void
     */
    public function restore($id)
    {
        if (is_array($id)) {
            $this->model->withTrashed()->whereIn('id', $id)->restore();
        } else {
            $this->model->withTrashed()->findOrFail($id)->restore();
        }
    }

    /**
     * Permanently delete model resource.
     *
     * @param integer|array $id
     * @return void
     */
    public function delete($id)
    {
        if (is_array($id)) {
            $users = $this->model->withTrashed()->whereIn('id', $id)->get();

            foreach ($users as $user) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $user->forceDelete();
            }
        } else {
            $user = $this->model->withTrashed()->findOrFail($id);
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->forceDelete();
        }
    }

    /**
     * Generate random hash key.
     *
     * @param string $key
     * @return string
     */
    public function hash(string $key): string
    {
        return Hash::make($key);
    }

    /**
     * Upload the given file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function upload(UploadedFile $file)
    {
        return $file->store('users', 'public');
    }
}
