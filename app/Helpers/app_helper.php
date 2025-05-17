<?php

use App\Data\MetricName;
use App\Data\NtfyCommandName;
use App\Data\OpenMetric;
use App\Entities\DogMetric;
use App\Entities\PushNotification;
use Assert\Assert;
use Symfony\Component\Filesystem\Path;

function setUserFlashMessage(string $message)
{
    $messages = getUserFlashMessage() ?? [];
    $messages[] = $message;
    session()->set('__list_messages', $messages);
    session()->markAsTempdata('__list_messages', 10); // 10 seconds life.
}

function getUserFlashMessage(): array
{
    $msgs = array_unique(session('__list_messages') ?? []);
    _deleteKey('__list_messages');

    return $msgs;
}

/**
 * Return now with ms precision.
 */
function now_millis(): int
{
    return floor(microtime(true) * 1000);
}

/**
 * Convert a id or name to label.
 *
 * `foo_bar` will be converted to `Foo Bar`.
 */
function nameToLabel(string $name): string
{
    return implode(' ', array_map(fn (string $x): string => ucfirst($x), explode('_', $name)));
}

/**
 * Show go back button after displaying message..
 */
function goBack(?string $message = null, ?string $href = null): string
{
    $href = $href ?? previous_url();

    $html = '<div class="mt-3">';
    if($message) {
        $html .= "<span class='display-6'>{$message}</span>";
    }
    $html .= "<a class='btn btn-link' href='" . $href . "'>Go Back</a>";
    $html .= "</div>";

    return $html;
}

/**
 * Convert a given string to database datetime.
 */
function dbDateTime(string $datetime, bool $local = false)
{
    date_default_timezone_set('UTC');
    if ($local) {
        date_default_timezone_set('Asia/Kolkata');
    }

    return date('Y-m-d h:i:s', strtotime($datetime));
}

function base64_url_encode(string $input): string
{
    return strtr(base64_encode($input), '+/=', '-_.');
}

function base64_url_decode(string $input): string
{
    return base64_decode(strtr($input, '-_.', '+/='));
}

/**
 * Active dog or current dog.
 */
function activeDog(): ?string
{
    return _getKeyVal('active_dog');
}

/**
 * Current session.
 */
function activeSession(): ?string
{
    return _getKeyVal('active_session');
}

function runMode(): ?string
{
    return _getKeyVal('run_mode');
}

function setRunMode(string $runMode)
{
    _setKeyVal('run_mode', $runMode);
}

/**
 * Current user.
 */
function user(): ?string
{
    return _getKeyVal('auth_user');
}

/**
 * Set current user.
 */
function setUser(string $email)
{
    _setKeyVal('auth_user', $email);
}

/**
 * Set current sample_code.
 */
function setCurrentSampleCode(string $sampleCode)
{
    _setKeyVal('current_sample_code', $sampleCode);
}

/**
 * Get current sample_code.
 */
function currentSampleCode(): ?string
{
    return session('current_sample_code');
}

function currentPatientUid(): ?string
{
    return _getKeyVal('current_patient_uid');
}

function setCurrentPatientUid(string $value)
{
    _setKeyVal('current_patient_uid', $value);
}

function currentSampleUid(): ?string
{
    return _getKeyVal('current_sample_uid');
}

function setCurrentSampleUid(string $value)
{
    _setKeyVal('current_sample_uid', $value);
}

function currentRecordUid(): ?string
{
    return _getKeyVal('current_record_uid');
}

function setCurrentRecordUid(string $value)
{
    _setKeyVal('current_record_uid', $value);
}

function clearEhrSession()
{
    log_message('info', 'Clearing EHR session');
    _deleteKey('current_sample_uid');
    _deleteKey('current_record_uid');
    _deleteKey('current_sample_code');
    _deleteKey('current_patient_uid');
}

/**
 * @brief Set current barrier mode and send a metric to TSDB.
 */
function setBarrierMode(string $barrierMode): void
{
    $oldValue = barrierMode();
    log_message('info', "Setting barrier_mode={$barrierMode}");
    Assert::that($barrierMode)->string();
    _setKeyVal('barrier_mode', $barrierMode);

    // also log it into TSDB if value has changed.
    if ($oldValue !== $barrierMode) {
        $metric = OpenMetric::create(MetricName::ArenaCurrentBarrierMode->value, 1, tags: [
            'barrier_mode' => $barrierMode,
        ]);
        $metric->write();

        if ($oldValue) {
            $metric = OpenMetric::create(MetricName::ArenaCurrentBarrierMode->value, 0, tags: [
                'barrier_mode' => $oldValue,
            ]);
            $metric->write();
        }
    } else {
        log_message('info', "New value of barrier_mode {$barrierMode} is same as previously set value");
    }
}

function barrierMode(): ?string
{
    return _getKeyVal('barrier_mode');
}

/**
 * @brief Set caliberation run
 */
function setCaliberationRun(string $caliberationRun): void
{
    log_message('info', "Setting caliberationRun={$caliberationRun}");
    Assert::that($caliberationRun)->string();
    _setKeyVal('is_caliberation_run', $caliberationRun);

    $value = 'yes' === strtolower($caliberationRun) ? 1 : 0;
    $metric = OpenMetric::create(MetricName::ArenaIsCalibrationRun->value, $value);
    $metric->write();
}

function caliberationRun(): ?string
{
    return _getKeyVal('is_caliberation_run');
}

/**
 * @param string|array<string>|int|bool $value
 */
function _setKeyVal(string $key, string|array|int|bool $value): void
{
    log_message('debug', "Saving {$key}=`" . json_encode($value) . '`.');
    session()->set($key, $value);
}

/**
 * @return string|array<string>|int|bool $value
 */
function _getKeyVal(string $key): string|array|int|bool|null
{
    Assert::that($key)->notEmpty();

    return session($key);
}

function _deleteKey(string $key): void
{
    session()->remove($key);
}

/**
 * Get TrainerSession.
 */
function trainerSession(): ?App\Entities\TrainerSession
{
    return session('trainer_session');
}

/**
 * Set a sample sniffed state.
 */
function setSampleSniffedState(string $sampleCode, bool|string $value, int $ttl = 8 * 3600): void
{
    $sniffState = service('appcache')->getItem(__sniffedStateKey($sampleCode));
    $sniffState->expiresAfter($ttl);
    $sniffState->tag('trainer');
    $sniffState->set($value);
    service('appcache')->save($sniffState);

    Assert::that($value)->eq(sampleSniffedState($sampleCode));
}

/**
 * Get a sample sniffed state.
 */
function sampleSniffedState(string $sampleCode): bool|string
{
    $sniffState = service('appcache')->getItem(__sniffedStateKey($sampleCode));
    $value = $sniffState->get() ?? false;
    log_message('debug', "sniffstate {$sampleCode} = {$value}");

    return $value;
}

/**
 * Trigger various events on Dog's indication.
 */
function triggerEventsOnDogIndication(
    string $dogName,
    string $sampleCode,
    bool $correct,
    int $ttl = 8 * 3600 /* 8 hours */,
): void
{
    // Change classification state so that the UI can show thumb-up.
    $classificationState = service('appcache')->getItem(__isDogCorrectKey($dogName, $sampleCode));
    $classificationState->expiresAfter($ttl);
    $classificationState->tag('trainer');
    $classificationState->set($correct);
    service('appcache')->save($classificationState);

    // Send classification metric.
    $metric = new DogMetric(
        dogName: $dogName,
        metricName: MetricName::DogIsClassificationCorrect,
        value: intval($correct)
    );
    $metric->upload();

    // Send "did Dog it correct" notification. The receiver than play tones,
    // change state of its app, or dance a little!
    $notification = PushNotification::dogNtfyCommand(
        command: $correct ? NtfyCommandName::AlertDogIndicationCorrect : NtfyCommandName::AlertDogIndicationWrong,
        sampleCode: $sampleCode,
    );
    $notification->send();
}

/**
 * @brief Query in cache if a particular dog got the sample classification
 * correct.
 */
function didDogGotClassificationCorrect(string $dogName, string $sampleCode): ?bool
{
    $classificationState = service('appcache')->getItem(__isDogCorrectKey($dogName, $sampleCode));

    return $classificationState->get();
}

/**
 * @brief Clear cache related to trainer.
 */
function clearTrainerCache(): void
{
    service('appcache')->invalidateTags(['trainer']);
}

function __sniffedStateKey(string $sampleCode): string
{
    return 'sample_sniffed_state__' . $sampleCode;
}

function __isDogCorrectKey(string $dogName, string $sampleCode): string
{
    return 'is_dog_correct__' . $dogName . '__' . $sampleCode;
}

/**
 * @param array<string > $roles
 */
function setUserRoles(array $roles): void
{
    _setKeyVal('current_user_roles', $roles);
}

/**
 * @return array<string>
 */
function userRoles(): array
{
    return _getKeyVal('current_user_roles') ?? [];
} 

/**
 * Check if current user is admin.
 */
function isAdmin(): bool
{
    return count(array_intersect(['ROOT', 'COORDINATOR'], _getKeyVal('current_user_roles') ?? [])) > 0;
} 

/**
 * Remove duplicates from an array using given keys.
 */
function uniqueArrayOfObjects(array $arr, array $keys): array
{
    $result = [];
    foreach ($arr as $_id => $object) {
        unset($_id); // to shut-up linter.
        // log_message("debug", ">> $_id " . json_encode($object));
        $hash = '';
        foreach ($keys as $key) {
            $hash .= $object->{$key};
        }
        // last value will be kept.
        $result[$hash] = $object;
    }

    return array_values($result);
}

/**
 * Remove duplicates from an array of array given specific keys.
 *
 * @var array<array<string, mixed> > $arr
 */
function uniqueArrayOfArray(array $arrs, array $keys): array
{
    $result = [];
    foreach ($arrs as $_id => $arr) {
        unset($_id); // to shut-up linter.
        // log_message("debug", ">> $_id " . json_encode($object));
        $hash = '';
        foreach ($keys as $key) {
            $hash .= $arr[$key];
        }
        // last value will be kept.
        $result[$hash] = $arr;
    }

    return array_values($result);
}

/**
 * Remove all - from uuid.
 */
function uuidWithoutHyphen(string $uuid): string
{
    return str_replace('-', '', $uuid);
}

/**
 * @brief Mark a row deleted by setting appropriate fields.
 */
function markDeleted(array &$post, string $primaryKey = 'uid')
{
    Assert::that($post)->keyIsset($primaryKey);
    $post['is_valid'] = false;
    $post['reason_for_invalid'] = 'DELETED_BY_USER';
}

/**
 * @brief Mark a row undeleted.
 */
function markUndeleted(array &$post, string $primaryKey = 'uid')
{
    Assert::that($post)->keyIsset($primaryKey);
    $post['is_valid'] = true;
    $post['reason_for_invalid'] = null;
}

function storageForConvertedFile(string $filename = ''): string 
{
    return Path::canonicalize(WRITEPATH . 'converted/' . "$filename");
}

function getDataURI(string $imagePath): string
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->file($imagePath);
    return 'data:' . $type . ';base64,' . base64_encode(file_get_contents($imagePath));
}

/**
 * Are we running in production mode.
 */
function isProduction(): bool 
{
    return strtolower(trim(getenv('CI_ENVIRONMENT') ?? 'production')) === 'production';
}

/**
 * @return array<string>
 */
function supportedImageFormats(): array 
{
    $imagick = new \Imagick();
    return $imagick->queryFormats();
}

