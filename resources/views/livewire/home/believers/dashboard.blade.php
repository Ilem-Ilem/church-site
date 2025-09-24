<?php
//TODO: fix the classes dashboard for updating a class
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, User, StudentClasses, AcademyClases};

new #[Layout('components.layouts.layout')] class extends Component {
    public $classes;
    public $student_classes;
    public $classDone;
    public $classNotDone;

    public function mount()
    {
        $this->classes = AcademyClases::all();
        $this->student_classes = StudentClasses::where('user_id', auth()->user()->id)->get();
        $this->classDone = json_decode(StudentClasses::where('user_id', auth()->user()->id)->first()->class_completed);

        $this->classNotDone = array_diff($this->classes->pluck('id')->toArray(), $this->classDone);
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

        .btn-dark-blue {
            background-color: #0B3D91;
            /* Dark blue */
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.4rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-dark-blue:hover {
            background-color: #092F6B;
            /* Darker shade on hover */
            transform: translateY(-2px);
            color: #ffffff;
        }

        .btn-dark-blue:active {
            background-color: #081F4F;
            /* Even darker when clicked */
            transform: translateY(0);
        }
    </style>

    <div class="main-container">
        <div class="container">
            <div class="container mt-5">

                <button class="btn btn-dark-blue float-right" {{ count($classNotDone) != 0 ? 'disabled' : '' }}>
                    Print Certificate
                </button>

                <h2 class="mt-5">Class Progress</h2>
                <div class="table-responsive-sm">
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
                            @foreach ($classes as $key => $class)
                                <tr>
                                    <th scope="row">{{ $key + 1 }}</th>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->date }}</td>
                                    @if (in_array($class->id, $classDone))
                                        <td><span class="badge bg-success">Completed</span></td>
                                    @else
                                        <td><span class="badge bg-danger">Pending</span></td>
                                    @endif
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
</div>
