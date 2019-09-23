<?php

declare(strict_types=1);

namespace Orchid\Screen\Contents;

use Illuminate\Support\Arr;
use Orchid\Screen\Repository;
use Orchid\Screen\Layouts\Base;
use Orchid\Access\UserInterface;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Contracts\ActionContract;

/**
 * Class CardContent.
 */
class CardContent extends Base
{
    /**
     * @var Repository
     */
    protected $query;

    /**
     * @var string
     */
    protected $template = 'platform::contents.card';

    /**
     * @return string|null
     */
    protected function title(): ?string
    {
        return 'Conversation about takeaways from annual SharePoint conference';
    }

    /**
     * @return string|null
     */
    protected function descriptions(): ?string
    {
        return 'This is a wider card with supporting text below as a natural lead-in to additional content. This content is a
                  little bit longer. This is a wider card with supporting text below as a natural lead-in to additional content. This
                  content is a little bit longer. This is a wider card with supporting text below as a natural lead-in to additional
                  content. This content is a little bit longer.';
    }

    /**
     * @return string|null
     */
    protected function image(): ?string
    {
        return 'https://picsum.photos/600/300';
    }

    /**
     * @return mixed
     */
    protected function status()
    {
        return $this->query->get('orchid.title');
    }

    /**
     * @return User|User[]|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function users()
    {
        return [
            Auth::user(),
            Auth::user(),
            Auth::user(),
        ];
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    protected function linkForUser(UserInterface $user)
    {
        return route('platform.systems.users.edit', $user);
    }

    /**
     * @return array
     */
    protected function commandBar(): array
    {
        return [
            Button::make('Example Button')
                ->method('example')
                ->icon('icon-bag'),

            Button::make('Example Button')
                ->method('example')
                ->icon('icon-bag'),
        ];
    }

    /**
     * @param \Orchid\Screen\Repository $repository
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function build(Repository $repository)
    {
        $this->query = $repository;

        return view($this->template, [
            'title'        => $this->title(),
            'descriptions' => $this->descriptions(),
            'image'        => $this->image(),
            'commandBar'   => $this->buildCommandBar(),
            'status'       => $this->status(),
            'users'        => $this->buildUserBar(),
        ]);
    }

    /**
     * @return array
     */
    private function buildCommandBar(): array
    {
        return collect($this->commandBar())
            ->map(function (ActionContract $command) {
                return $command->build($this->query);
            })->all();
    }

    /**
     * @return array
     */
    private function buildUserBar(): array
    {
        $users = Arr::wrap($this->users());

        return collect($users)->map(function (UserInterface $user) {
            return [
               'avatar' => $user->getAvatar(),
               'name'   => $user->getNameTitle(),
               'sub'    => $user->getSubTitle(),
               'link'   => $this->linkForUser($user),
           ];
        })->all();
    }
}
