<x-app-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">Create Client</h1>

        <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-medium">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input w-full" required>
            </div>

            <div>
                <label for="email" class="block font-medium">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input w-full">
            </div>

            <div>
                <label for="phone" class="block font-medium">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input w-full">
            </div>

            <div>
                <label for="fax" class="block font-medium">Fax</label>
                <input type="text" name="fax" id="fax" value="{{ old('fax') }}" class="form-input w-full">
            </div>

            <div>
                <label for="address" class="block font-medium">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-input w-full">
            </div>

            <div>
                <label for="website" class="block font-medium">Website</label>
                <input type="url" name="website" id="website" value="{{ old('website') }}" class="form-input w-full">
            </div>

            <div>
                <label for="trn" class="block font-medium">TRN</label>
                <input type="text" name="trn" id="trn" value="{{ old('trn') }}" class="form-input w-full">
            </div>

            <div>
                <label for="contact_person" class="block font-medium">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="form-input w-full">
            </div>

            <div>
                <label for="status" class="block font-medium">Status</label>
                <input type="text" name="status" id="status" value="{{ old('status') }}" class="form-input w-full">
            </div>

            <div>
                <label for="country" class="block font-medium">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country') }}" class="form-input w-full">
            </div>

            <div>
                <label for="type" class="block font-medium">Type</label>
                <input type="text" name="type" id="type" value="{{ old('type') }}" class="form-input w-full">
            </div>

            <div>
                <label for="notes" class="block font-medium">Notes</label>
                <textarea name="notes" id="notes" class="form-input w-full">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
