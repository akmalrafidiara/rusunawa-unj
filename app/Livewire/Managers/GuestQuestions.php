<?php

namespace App\Livewire\Managers;

use App\Models\GuestQuestion as GuestQuestionModel; // Use alias to avoid conflicts
use Livewire\Component;
use Livewire\WithPagination;

class GuestQuestions extends Component
{
    // Traits
    use WithPagination;

    // Filter properties (for search filtering)
    public $search = '';
    // roleFilter property removed as it's not in GuestQuestion model

    // Pagination and sorting properties
    public $perPage = 10;
    public $orderBy = 'created_at'; // Common for models, assuming GuestQuestion has this timestamp
    public $sort = 'desc'; // 'desc' or 'asc'

    // Query string properties to keep the URL updated with filter, sort, and pagination state
    protected $queryString = [
        'search' => ['except' => ''],
        // 'roleFilter' removed from queryString
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    /**
     * Resets the pagination to the first page when any of the filter or sort
     * properties change. This is a Livewire lifecycle hook.
     *
     * @param string $propertyName The name of the property that was updated.
     * @return void
     */
    public function updating($propertyName)
    {
        // Check if the updated property is part of our filters or sorting criteria.
        // If so, reset the page to 1 to ensure a consistent experience when changing filters/sorts.
        if (in_array($propertyName, ['search', 'perPage', 'orderBy', 'sort'])) {
            $this->resetPage();
        }
    }

    /**
     * Render the component view.
     * This method fetches the data based on the current search, filter,
     * sorting, and pagination parameters.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Build the query for guest questions
        $guestQuestions = GuestQuestionModel::query() // Changed to GuestQuestionModel
            // Apply search filter if 'search' property is not empty
            ->when($this->search, function ($query) {
                // Search by fullName (case-insensitive and partial match)
                // Assuming you want to search by fullName based on your GuestQuestion model
                $query->where('fullName', 'like', "%{$this->search}%");
            })
            // roleFilter logic removed as GuestQuestion model doesn't have a 'role' field
            // Apply sorting based on 'orderBy' and 'sort' properties
            ->orderBy($this->orderBy, $this->sort)
            // Paginate the results with 'perPage' items per page
            ->paginate($this->perPage);

        // Return the view, passing the paginated guest questions data to it
        // You'll need to adapt your Blade view to iterate over $guestQuestions instead of $contacts
        return view('livewire.managers.contents.guest-questions.index', compact('guestQuestions'));
    }

    /**
     * Method to change the sorting column.
     * If the new column is the same as the current, toggle the sort direction.
     * Otherwise, set the new column and default to ascending sort.
     *
     * @param string $column The column name to sort by.
     * @return void
     */
    public function sortBy($column)
    {
        if ($this->orderBy === $column) {
            // If clicking the same column, toggle sort direction
            $this->sort = ($this->sort === 'asc') ? 'desc' : 'asc';
        } else {
            // If clicking a new column, set it as orderBy and default to asc
            $this->orderBy = $column;
            $this->sort = 'asc';
        }
        $this->resetPage(); // Reset pagination when sorting changes
    }
}
