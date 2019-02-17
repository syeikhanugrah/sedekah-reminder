<?php

namespace App\Command;

use App\Entity\LogSmsKeluar;
use App\Entity\Pengingat;
use App\Repository\LogSmsKeluarRepository;
use App\Repository\PengingatRepository;
use App\Service\PengirimPesan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PengirimNotifikasiPengingatCommand extends Command
{
    protected static $defaultName = 'app:kirim-notifikasi-pengingat';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var PengingatRepository
     */
    private $pengingatRepository;

    /**
     * @var LogSmsKeluarRepository
     */
    private $logSmsKeluarRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PengirimPesan
     */
    private $pengirimPesan;

    public function __construct(
        PengingatRepository $pengingatRepository,
        LogSmsKeluarRepository $logSmsKeluarRepository,
        EntityManagerInterface $entityManager,
        PengirimPesan $pengirimPesan
    ) {
        parent::__construct();

        $this->pengingatRepository = $pengingatRepository;
        $this->logSmsKeluarRepository = $logSmsKeluarRepository;
        $this->entityManager = $entityManager;
        $this->pengirimPesan = $pengirimPesan;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Mengirim notifikasi pengingat kepada user');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tanggalSekarang = new \DateTime();
        $semuaPengingat = $this->pengingatRepository->findAll();

        $pengirimPesan = $this->pengirimPesan;

        /** @var Pengingat $pengingat */
        foreach ($semuaPengingat as $pengingat) {
            $logSmsKeluar = $this->logSmsKeluarRepository->findByLogPengingatSekarang($pengingat);

            if ($logSmsKeluar instanceof LogSmsKeluar) {
                continue;
            }

            $tanggalAwalPengingat = $pengingat->getTanggalAwal();
            $tanggalAkhirPengingat = $pengingat->isSelamanya()
                // Agar objek tanggal akhir tidak kosong maka dibuat
                // objek tanggal maksimal, tanggal ini merupakan tanggal maksimal di sistem operasi 32 bit
                // Lihat: http://php.net/manual/en/function.date.php#refsect1-function.date-changelog
                ? \DateTime::createFromFormat('Y-m-d', '2038-01-19')
                : $pengingat->getTanggalAkhir()
            ;

            if (!($tanggalSekarang->format('Ymd') >= $tanggalAwalPengingat->format('Ymd') &&
                $tanggalSekarang->format('Ymd') <= $tanggalAkhirPengingat->format('Ymd'))
            ) {
                continue;
            }

            if ($pengingat->getPerulangan() == Pengingat::PENGINGAT_MINGGUAN) {
                if (!($tanggalSekarang->format('N') == $pengingat->getMingguanHariKe())) {
                    continue;
                }
            } elseif ($pengingat->getPerulangan() == Pengingat::PENGINGAT_BULANAN) {
                if (!($tanggalSekarang->format('j') == $pengingat->getBulananHariKe())) {
                    continue;
                }
            }

            $user = $pengingat->getUser();
            $pesanPengingat = $this->getPesanPengingat();
            $pesanPengingat = str_replace('%user%', $user->getNamaLengkap(), $pesanPengingat);
            $pesanPengingat = str_replace('%judul-pengingat%', $pengingat->getJudul(), $pesanPengingat);

            $pengirimPesan->setPesan($pesanPengingat);
            $pengirimPesan->setTujuan($user->getNomorHp());
            $pengirimPesan->kirim();

            $this->catatLogSmsKeluar([
                'tujuan' => $user->getNomorHp(),
                'pesan' => $pesanPengingat,
                'pengingat' => $pengingat,
            ]);
        }
    }

    private function catatLogSmsKeluar(array $data): void
    {
        $logSmsKeluar = new LogSmsKeluar();

        $logSmsKeluar->setTujuan($data['tujuan']);
        $logSmsKeluar->setPesan($data['pesan']);
        $logSmsKeluar->setPengingat($data['pengingat']);
        $logSmsKeluar->setTanggal(new \DateTime());

        $em = $this->entityManager;
        $em->persist($logSmsKeluar);
        $em->flush();
    }

    private function getPesanPengingat(): string
    {
        return 'Assalamu\'alaikum %user%, ini hanya pengingat bahwa Anda memiliki komitmen %judul-pengingat% pada hari ini.';
    }
}
