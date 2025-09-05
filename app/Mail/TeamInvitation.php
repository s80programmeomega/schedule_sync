<?php

namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TeamMember $teamMember
    ) {}

    public function build()
    {
        return $this->subject("You're invited to join {$this->teamMember->team->name}")
            ->view('emails.team-invitation')
            ->with([
                'team' => $this->teamMember->team,
                'inviter' => $this->teamMember->invitedBy,
                'role' => $this->teamMember->role_display,
                'acceptUrl' => route('team.invitation.accept', $this->teamMember->invitation_token),
                'expiresAt' => $this->teamMember->invitation_expires_at,
            ]);
    }
}
