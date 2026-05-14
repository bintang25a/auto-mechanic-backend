<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $complaint_number
 * @property string $customer_id
 * @property string $queue_id
 * @property string $vehicle
 * @property string $license_number
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $customer
 * @property-read \App\Models\Queue $queue
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Symptom> $symptoms
 * @property-read int|null $symptoms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereComplaintNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereLicenseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereQueueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Complaint whereVehicle($value)
 */
	class Complaint extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $damage_code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Symptom> $symptoms
 * @property-read int|null $symptoms_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage whereDamageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Damage whereUpdatedAt($value)
 */
	class Damage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $queue_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Complaint|null $complaint
 * @property-read \App\Models\User|null $mechanic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereQueueNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereUpdatedAt($value)
 */
	class Queue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $symptom_code
 * @property string $damage_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Damage $damage
 * @property-read \App\Models\Symptom $symptom
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereDamageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereSymptomCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rule whereUpdatedAt($value)
 */
	class Rule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $symptom_code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Damage> $damages
 * @property-read int|null $damages_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom whereSymptomCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Symptom whereUpdatedAt($value)
 */
	class Symptom extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $uid
 * @property string $name
 * @property string $password
 * @property string $email
 * @property string $phone_number
 * @property string $role
 * @property string|null $photo
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Complaint> $complaints
 * @property-read int|null $complaints_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Queue> $queues
 * @property-read int|null $queues_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

