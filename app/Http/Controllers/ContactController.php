<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactCollection;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdaterequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);

    }

    public function get(int $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Not Found',
                ],
            ], 404));
        }
            return new ContactResource($contact);
    }

    public function update(ContactUpdaterequest $request, int $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Not Found',
                ],
            ], 404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact));
    }

    public function delete(int $id)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Not Found',
                ],
            ], 404));
        }

        $contact->delete();

        return response()->json(['data' =>true], 200);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $query) use ($name) {
                    $query->where('first_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%");
                });
            }

            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', "%{$email}%");
            }

            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', "%{$phone}%");
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return (new ContactCollection($contacts));
    }
}
