<?php
/**
 * WordPress AJAX Routes.
 *
 * @package Depicter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Document Endpoints
// ======================================

// Updates a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/store', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@store' );

// Reverts a document to previous snapshots
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/revert', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@revert' );

// Retrieves a document
Depicter::route()->methods(['GET'])
	->where( 'ajax', 'depicter/document/show', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@show' );

// Check slug
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/slug/check', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@checkSlug' );

// Renames a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/name/change', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'DashboardAjaxController@changeName' );

// Retrieves a document
Depicter::route()->methods(['GET'])
	->where( 'ajax', 'depicter/document/localization', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:7200,browser')
	->handle( 'EditorAjaxController@getLocalization' );

// Get document markup
Depicter::route()->methods(['GET'])
	->where( 'ajax', 'depicter/document/render', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@render' );

Depicter::route()->methods(['GET'])
	->where( 'ajax', 'depicter/document/preview', true, false )
	->handle( 'EditorAjaxController@preview' );

// Get document data
Depicter::route()->methods(['GET'])
	->where( 'ajax', 'depicter/document/editor', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'EditorAjaxController@getEditorData' );

// ======================================

// Creates new document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/create', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'EditorAjaxController@create' );

// Retrieves list of documents
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/document/index', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@index' );

// Creates new document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/create', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@create' );

// Removes a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/destroy', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@destroy' );

// Duplicates a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/duplicate', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@duplicate' );

// Changes a document slug
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/slug/change', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@changeSlug' );


// Exports a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/export', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@export' );

// Imports a document
Depicter::route()->methods(['POST'])
	->where( 'ajax', 'depicter/document/import', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'DashboardAjaxController@import' );

// WP Resources
// ======================================

Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/wp/v1/posts/types', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'PostsAjaxController@getPostTypes' );

Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/wp/v1/posts', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'PostsAjaxController@getPosts' );

// Media Libraries
// ======================================

// Get list of all attachments
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/library/search/all', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'MediaLibraryAjaxController@query' );

// get list of all media library images
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/library/search/images', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'MediaLibraryAjaxController@images' );

// get list of all media library audios
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/library/search/audios', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'MediaLibraryAjaxController@audios' );

// get list of all media library videos
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/library/search/videos', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'MediaLibraryAjaxController@videos' );

// get list of all media library vectors
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/library/search/vectors', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'MediaLibraryAjaxController@vectors' );


// Third Parties Media
// ======================================

// Search Unsplash photos
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/search/images', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:260000,browser')
	->handle( 'MediaAssetsAPIAjaxController@searchImages' );

// Search Pixabay Videos
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/search/videos', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:260000,browser')
	->handle( 'MediaAssetsAPIAjaxController@searchVideos' );

// Search Pixabay Vector Photos
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/search/vectors', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:260000,browser')
	->handle( 'MediaAssetsAPIAjaxController@searchVectors' );

// Return hotlinks
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/media/get', true, true )
	->name('getMedia')
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:900,browser')
	->handle( 'MediaAssetsAPIAjaxController@getMedia' );

// Upload media file
Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/media/upload', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'FileUploaderController@uploadFile' );

// Search and retrieve animation presets
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/animations', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:86400,browser')
	->handle( 'CuratedAPIAjaxController@searchAnimations' );

// Retrieve animation phases
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/animations/categories', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:86400,browser')
	->handle( 'CuratedAPIAjaxController@getAnimationsCategories' );

// Search and retrieve element presets
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/elements', true, true )
	->middleware('csrf-api:depicter-editor' )
	->middleware('cache:86400,browser')
	->handle( 'CuratedAPIAjaxController@searchElements' );

// Search and retrieve document templates
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/document/templates', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->middleware('cache:3600,browser')
	->handle( 'CuratedAPIAjaxController@searchDocumentTemplates' );

// Retrieve document template categories
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/document/templates/categories', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->middleware('cache:3600,browser')
	->handle( 'CuratedAPIAjaxController@getDocumentTemplateCategories' );

// Preview a document template
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/document/templates/preview', true, true )
	->middleware('cache:7200,browser')
	->handle( 'CuratedAPIAjaxController@previewDocumentTemplate' );

// Import document template
Depicter::route()->methods(['GET'] )
	->where(  'ajax', 'depicter/assets/document/templates/import', true, true )
	->middleware('csrf-api:depicter-dashboard' )
	->handle( 'CuratedAPIAjaxController@importDocumentTemplate' );

// Send user feedbacks
Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/report/issue', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'ReportIssueAjaxController@sendIssue' );

Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/report/error', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'ReportIssueAjaxController@sendError' );

Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/security/csrf/generate', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'SecurityAjaxController@generateCsrfToken' );

Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/subscriber/store', true, true )
	->middleware('csrf-api:depicter-editor' )
	->handle( 'SubscriberAjaxController@store' );


// General
// ======================================

Depicter::route()->methods(['POST'] )
	->where(  'ajax', 'depicter/deactivate/feedback', true, true )
	->middleware('nonce')
	->handle( 'PluginDeactivationController@sendFeedback' );

