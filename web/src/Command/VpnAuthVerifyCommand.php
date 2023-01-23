<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Command;

use SohaJin\RedPacket2023\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('vpn:auth-user-pass-verify', 'For auth-user-pass-verify in OpenVPN')]
class VpnAuthVerifyCommand extends Command {
	public function __construct(
		private readonly UserRepository $userRepository
	) {
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$username = trim(getenv('username', true));
		$password = getenv('password', true);
		if(empty($username) || !$password) {
			$output->writeln('no auth info provided');
			return Command::FAILURE;
		}
		if(!($user = $this->userRepository->findOneByUsername($username))) {
			$output->writeln('user not found');
			return Command::FAILURE;
		}
		if($user->getVpnPassword() === $password) {
			$output->writeln('user authorized');
			return Command::SUCCESS;
		}
		$output->writeln('password wrong');
		return Command::FAILURE;
	}
}
