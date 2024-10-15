<div class="space-y-10">
    <div>
        <h2 class="text-xl font-medium">Choose a service from {{ $employee->name }}</h2>
        @if($employee->services)
            <div class="grid grid-col-2 md:grid-cols-5 gap-8 mt-6">
                @foreach($employee->services as $service)
                    <x-service :href="route('checkout', [$service])" :service="$service" />
                @endforeach

            </div>
        @else:
            <div class="grid grid-col-2 md:grid-cols-5 gap-8 mt-6">
                <p>No service found !</p>
            </div>
        @endif
    </div>
</div>
