<?php

/**
 * Ushahidi Platform Entity Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Usecase\SearchUsecase;

class SearchSet extends SearchUsecase
{

	// Usecase
	public function interact()
	{
		// Fetch an empty entity...
		$entity = $this->getEntity();

		// ... verify that the entity can be searched by the current user
		$this->verifySearchAuth($entity);

		// ... and get the search filters for this entity
		$search = $this->getSearch();

		// ... pass the search information to the repo
		$this->repo->setSearchParams($search);

		// ... get the results of the search
		$results = $this->repo->getSearchResults();

		// ... get the total count for the search
		$total = $this->repo->getSearchTotal();

		// ... remove any entities that cannot be seen
		$priv = 'search';
		foreach ($results as $idx => $entity) {
			if (!$this->auth->isAllowed($entity, $priv)) {
				unset($results[$idx]);
			}
		}

		// ... pass the search information to the formatter, for paging
		$this->formatter->setSearch($search, $total);

		// ... and return the formatted results.
		return $this->formatter->__invoke($results);
	}

}