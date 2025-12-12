<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrdersTable extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        $orders = Order::query()
            ->with(['pizza', 'toppings'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $innerQuery) use ($search) {
                    $innerQuery->where('customer_name', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $innerQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.orders-table', [
            'orders' => $orders,
            'search' => $search,
        ]);
    }
}
