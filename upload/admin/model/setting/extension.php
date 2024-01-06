<?php
namespace Opencart\Admin\Model\Setting;
/**
 * Class Extension
 *
 * @package Opencart\Admin\Model\Setting
 */
class Extension extends \Opencart\System\Engine\Model {
	/**
	 * Get Extensions
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getExtensions(): array {
		$query = $this->db->query("SELECT DISTINCT `extension` FROM `" . DB_PREFIX . "extension`");

		return $query->rows;
	}

	/**
	 * Get Extensions By Type
	 *
	 * @param string $type
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getExtensionsByType(string $type): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($type) . "' ORDER BY `code` ASC");

		return $query->rows;
	}

	/**
	 * Get Extension By Code
	 *
	 * @param string $type
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 */
	public function getExtensionByCode(string $type, string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	/**
	 * Get Total Extensions By Extension
	 *
	 * @param string $extension
	 *
	 * @return int
	 */
	public function getTotalExtensionsByExtension(string $extension): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "extension` WHERE `extension` = '" . $this->db->escape($extension) . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Install
	 *
	 * @param string $type
	 * @param string $extension
	 * @param string $code
	 *
	 * @return void
	 */
	public function install(string $type, string $extension, string $code): void {
		$extensions = $this->getExtensionsByType($type);

		$codes = array_column($extensions, 'code');

		if (!in_array($code, $codes)) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `extension` = '" . $this->db->escape($extension) . "', `type` = '" . $this->db->escape($type) . "', `code` = '" . $this->db->escape($code) . "'");
		}
	}

	/**
	 * Uninstall
	 *
	 * @param string $type
	 * @param string $code
	 *
	 * @return void
	 */
	public function uninstall(string $type, string $code): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = '" . $this->db->escape($type . '_' . $code) . "'");
	}

	/**
	 * Add Install
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public function addInstall(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension_install` SET `extension_id` = '" . (int)$data['extension_id'] . "', `extension_download_id` = '" . (int)$data['extension_download_id'] . "', `name` = '" . $this->db->escape($data['name']) . "', `description` = '" . $this->db->escape($data['description']) . "', `code` = '" . $this->db->escape($data['code']) . "', `version` = '" . $this->db->escape($data['version']) . "', `author` = '" . $this->db->escape($data['author']) . "', `link` = '" . $this->db->escape($data['link']) . "', `status` = '0', `date_added` = NOW()");

		return $this->db->getLastId();
	}

	/**
	 * Delete Install
	 *
	 * @param int $extension_install_id
	 *
	 * @return void
	 */
	public function deleteInstall(int $extension_install_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension_install` WHERE `extension_install_id` = '" . (int)$extension_install_id . "'");
	}

	/**
	 * Edit Status
	 *
	 * @param int  $extension_install_id
	 * @param bool $status
	 *
	 * @return void
	 */
	public function editStatus(int $extension_install_id, bool $status): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "extension_install` SET `status` = '" . (bool)$status . "' WHERE `extension_install_id` = '" . (int)$extension_install_id . "'");
	}

	/**
	 * Get Install
	 *
	 * @param int $extension_install_id
	 *
	 * @return array<string, mixed>
	 */
	public function getInstall(int $extension_install_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `extension_install_id` = '" . (int)$extension_install_id . "'");

		return $query->row;
	}

	/**
	 * Get Install By Extension Download ID
	 *
	 * @param int $extension_download_id
	 *
	 * @return array<string, mixed>
	 */
	public function getInstallByExtensionDownloadId(int $extension_download_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `extension_download_id` = '" . (int)$extension_download_id . "'");

		return $query->row;
	}

	/**
	 * Get Install By Code
	 *
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 */
	public function getInstallByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_install` WHERE `code` = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	/**
	 * Get Installs
	 *
	 * @param array $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getInstalls(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "extension_install`";

		if (!empty($data['filter_extension_download_id'])) {
			$sql .= " WHERE `extension_download_id` = '" . (int)$data['filter_extension_download_id'] . "'";
		}

		$sort_data = [
			'name',
			'version',
			'date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `date_added`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Total Installs
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public function getTotalInstalls(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "extension_install`";

		if (!empty($data['filter_extension_download_id'])) {
			$sql .= " WHERE `extension_download_id` = '" . (int)$data['filter_extension_download_id'] . "'";
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}

	/**
	 * Add Path
	 *
	 * @param int    $extension_install_id
	 * @param string $path
	 *
	 * @return void
	 */
	public function addPath(int $extension_install_id, string $path): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "extension_path` SET `extension_install_id` = '" . (int)$extension_install_id . "', `path` = '" . $this->db->escape($path) . "'");
	}

	/**
	 * Delete Path
	 *
	 * @param int $extension_path_id
	 *
	 * @return void
	 */
	public function deletePath(int $extension_path_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "extension_path` WHERE `extension_path_id` = '" . (int)$extension_path_id . "'");
	}

	/**
	 * Get Path By Extension Install ID
	 *
	 * @param int $extension_install_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getPathsByExtensionInstallId(int $extension_install_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_path` WHERE `extension_install_id` = '" . (int)$extension_install_id . "' ORDER BY `extension_path_id` ASC");

		return $query->rows;
	}

	/**
	 * Get Paths
	 *
	 * @param string $path
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getPaths(string $path): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension_path` WHERE `path` LIKE '" . $this->db->escape($path) . "' ORDER BY `path` ASC");

		return $query->rows;
	}

	/**
	 * Get Total Paths
	 *
	 * @param string $path
	 *
	 * @return int
	 */
	public function getTotalPaths(string $path): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "extension_path` WHERE `path` LIKE '" . $this->db->escape($path) . "'");

		return (int)$query->row['total'];
	}
}
