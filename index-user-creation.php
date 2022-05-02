<?php

class UserData {
    public string $id;

    public function __construct(
        public string $username,
        public string $email
    ) {
    }
}

class UsernameValidator {
    static function validate(string $username): bool {
        if (empty($username)) {
            throw new ErrorException('Invalid Username');
        }

        return true;
    }
}


class EmailValidator {
    static function validate(string $email): bool {
        if (empty($email)) {
            throw new ErrorException('Invalid E-mail');
        }

        return true;
    }
}

class User {
    public function __construct(
        private string $username = '',
        private string $email = '',
    ) {
    }

    public static function create(UserData $userData): self {
        UsernameValidator::validate($userData->username);
        EmailValidator::validate($userData->email);
        return new self($userData->username, $userData->email);
    }
}

interface UserRepository {
    public function create(UserData $userData): void;
    public function getAll(): array;
}

class InMemoryUserRepository implements UserRepository {
    private array $users = [];

    public function __construct(private User $user) {
    }

    public function create(UserData $userData): void {
        $this->user->create($userData);
        $userData->id = uniqid();
        $this->users[] = $userData;
    }

    public function getAll(): array {
        return $this->users;
    }
}

class RegisterUserUseCase {
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function execute(UserData $userData): void {
        $this->userRepository->create($userData);
    }
}

class GetAllUsersUseCase {
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function execute(): array {
        return $this->userRepository->getAll();
    }
}

$user = new User();

$userRepo = new InMemoryUserRepository($user);

$userData = new UserData('fake-usename', 'fake-email@email.com');

(new RegisterUserUseCase($userRepo))->execute($userData);

$userData = new UserData('fake-2-usename', 'fake-2-email@email.com');

(new RegisterUserUseCase($userRepo))->execute($userData);

$users = (new GetAllUsersUseCase($userRepo))->execute();

var_dump($users);
