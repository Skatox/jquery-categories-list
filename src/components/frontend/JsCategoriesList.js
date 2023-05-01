/**
 * WordPress dependencies
 */
import { useContext, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const JsCategoriesList = ( { attributes } ) => {
  return (
    <p>{JSON.stringify(attributes)}</p>
  );
}

export default JsCategoriesList;
