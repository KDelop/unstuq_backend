<?php

namespace App\Repositories\DateIdea;

use App\Repositories\DateIdea\DateIdeaRepositoryInterface;
use App\Repositories\Eloquent\Repository;
use App\Models\DateIdea;
use App\Models\DateIdeaLike;


class DateIdeaRepository extends Repository implements DateIdeaRepositoryInterface
{
    protected $dateIdea;
    protected $dateIdeaLike;

    /**
     * DateIdeaRepository constructor.
     *
     * @param DateIdea $dateIdea
     * @param DateIdeaLike $dateIdeaLike
     */
    public function __construct(DateIdea $dateIdea, DateIdeaLike $dateIdeaLike)
    {
        $this->dateIdea = $dateIdea;
        $this->dateIdeaLike = $dateIdeaLike;
    }

    public function get_date_ideas($userId)
    {
        $dateId =  $this->dateIdea->get()->random(20)->pluck('id');

        $dateIdeas = $this->dateIdea->select('id','title','image','likes','description','category','submitted_by','difficulty')->whereIn('id',$dateId)->orderBy('likes','DESC')->get();
        $data = $dateIdeas;
        foreach ($data as $key => $Idea) {
            $like = $this->dateIdeaLike->where('date_idea_id', $Idea->id)->where('user_id', $userId)->first();
            if(!empty($like)) {
                $data[$key]['user_like_status'] = 1;
            } else {
                $data[$key]['user_like_status'] = 0;
            }
        }
        return $data;
    }

    public function get_date_idea($userId, $id)
    {
        $data =  $this->dateIdea->find($id);
        if(!empty($data)) {
            $like = $this->dateIdeaLike->where('date_idea_id', $id)->where('user_id', $userId)->first();
            $dateIdea = $data;
            if (!empty($like)) {
                $dateIdea['user_like_status'] = 1;
            } else {
                $dateIdea['user_like_status'] = 0;
            }
            return $dateIdea;
        } else {
            return null;
        }


    }

    public function save_date_idea_like($request)
    {
        $result = $this->dateIdeaLike->create($request->all());
        $dateIdea = $this->dateIdea->find($result->date_idea_id);
        if(!empty($dateIdea)) {
            $dateIdea->update(['likes' => $dateIdea->likes + 1]);
        }
        return $result;
    }


}
