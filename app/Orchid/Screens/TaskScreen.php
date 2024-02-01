<?php

namespace App\Orchid\Screens;

use App\Models\Task;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class TaskScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'tasks' => Task::latest()->get(),
        ];
    }

    /**
     * O nome a ser exibido no cabeçalho.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Tarefas';
    }

    /**
     * A descrição da tela exibida no cabeçalho.
     */
    public function description(): ?string
    {
        return 'Tela para manipular tarefas';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Adicionar tarefa')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('tasks', [
                TD::make('name'),

                TD::make('Ações')
                ->alignRight()
                ->render(function (Task $task) {
                    return Button::make('Deletar Tarefa')
                        ->confirm('Após deletar, não será possível recuperar a tarefa.')
                        ->method('delete', ['task' => $task->id]);
                }),
            ]),

            Layout::modal('taskModal', Layout::rows([
                Input::make('task.name')
                    ->title('Nome')
                    ->placeholder('Digite o nome da tarefa aqui.')
                    ->help('O nome da tarefa a ser criada.'),
            ]))
                ->title('Criar Tarefa')
                ->applyButton('Adicionar Tarefa'),
        ];
    }

    /**
     * Cria uma nova tarefa.
     */
        public function create(Request $request)
    {
        /* Validação dos dados */
        $request->validate([
            'task.name' => 'required|max:255',
        ]);

        /* Salvando a tarefa */
        $task = new Task();
        $task->name = $request->input('task.name');
        $task->save();
    }

    /**
     * Deleta uma tarefa.
     */
    public function delete(Task $task)
    {
        $task->delete();
    }
}
