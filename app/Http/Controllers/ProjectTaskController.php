<?php

namespace Duplitron\Http\Controllers;

use Illuminate\Http\Request;

use Duplitron\Http\Requests;
use Duplitron\Http\Controllers\Controller;

use Duplitron\Project;
use Duplitron\ProjectTask;
use Duplitron\Jobs\PerformProjectTask;

class ProjectTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // Project ID is required
        if(!$request->has('project_id'))
        {
            // TODO: Real errors.
            return ['ERR: Project ID Required'];
        } else {
            $results = \DB::table('project_tasks')
                ->where('project_id', $request->input('project_id'));
        }
        return $results->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Create a new task object
        $task = new ProjectTask();
        $task->status_code = ProjectTask::STATUS_NEW;
        $task->attempts = 0;
        $task->project_id = $request->input('project_id');
        $task->type = $request->input('type');
        $task->save();

        // Dispatch a job for this task
        $this->dispatch(new PerformProjectTask($task));

        return $task;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $task = ProjectTask::find($id);
        return $task;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function cleanEverything() {

        $projects = Project::all();

        $tasks = array();

        foreach($projects as $project) {
            // Create a new task object
            $task = new ProjectTask();
            $task->status_code = ProjectTask::STATUS_NEW;
            $task->attempts = 0;
            $task->project_id = $project->id;
            $task->type = ProjectTask::TYPE_CLEAN;
            $task->save();

            $tasks[] = $task;

            // Dispatch a job for this task
            $this->dispatch(new PerformProjectTask($task));
        }
        return $tasks;
    }
}
