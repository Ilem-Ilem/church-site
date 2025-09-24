<?php
//TODO: fix the classes dashboard for updating a class
//TODO: complete this page
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, User, StudentClasses , AcademyClases};

new #[Layout('components.layouts.layout')] class extends Component {
    public $classes;
    public $student_classes;
    public $classNotDone;

    public function mount()
    {
        $this->classes = AcademyClases::all();
        $this->student_classes = StudentClasses::where('user_id', auth()->user()->id)->get();
    }
}; ?>

<div>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-con {
            /* max-width: 800px; */
            margin: 100px auto;
        }

        .section-title {
            font-size: 23px;
        }

        .navbar {
            background: linear-gradient(to right, #357be4, #294dc0) !important;
            height: 85px;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-holder {
            margin-top: 8rem;
            display: flex;
            justify-content: center;
            padding: 6px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: bold;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #664d03;
        }
    </style>
    <style>
        .table-hover-zoom tbody tr:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .main-container {
            margin-top: 10rem;
        }
    </style>

    <div class="main-container">
        <div class="container">
            <div class="container mt-5">
                <h2>Class Progress</h2>
                <table class="table table-hover-zoom table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Course</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Request Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classes as $key=> $class)
                            <tr>
                                <th scope="row">{{ $key+1 }}</th>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->date }}</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary">Request Update</button>
                                </td>
                            </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
