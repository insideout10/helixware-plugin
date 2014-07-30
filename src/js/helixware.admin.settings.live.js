angular.module( 'hewa', [] )
    .value( 'serverURL', hewa_admin_options.server_url )
    .value( 'endPoints', hewa_admin_options.end_points )
    .service( 'ApiService', [ 'serverURL', '$http', function ( serverURL, $http ) {

        return {

            /**
             * Performs a request to the remote server.
             *
             * @param method The request method.
             * @param path The request path.
             * @param callback The callback to call when data is received from the server.
             */
            request: function ( params ) {

                $http( { method: params.method, url: serverURL + params.path, data: params.data } )
                    .success( function( data, status, headers, config ) {
                        // this callback will be called asynchronously
                        // when the response is available
                        params.callback( data, status, headers, config  )
                    })

            },

            /**
             * Performs a GET request to the remote server.
             *
             * @param path The request path.
             * @param callback The callback to call when data is received from the server.
             */
            get : function( path, callback ) { return this.request( { method: 'GET', path: path, callback: callback } ); },

            /**
             * Performs a POST.
             *
             * @param path
             * @param data
             * @param callback
             * @returns {*}
             */
            post: function( path, data, callback ) { return this.request( { method: 'POST', path: path, callback: callback, data: data } ); },

            /**
             * Performs a DELETE.
             *
             * @param path
             * @param callback
             * @returns {*}
             */
            kill: function( path, callback ) { return this.request( { method: 'DELETE', path: path, callback: callback } )  },

            /**
             * Performs a PUT.
             *
             * @param path
             * @param data
             * @param callback
             * @returns {*}
             */
            update: function( path, data, callback ) { return this.request( { method: 'PUT', path: path, callback: callback, data: data } ); }
        };

    } ] )
/**
 * The LiveAssetService provides access to messages.
 */
    .service( 'LiveAssetService', [ 'ApiService', 'endPoints', function ( ApiService, endPoints ) {

        return {
            /**
             * List the messages.
             *
             * @param callback A function to call when data is received from the server.
             */
            list  : function( page, size, callback ) { ApiService.get( endPoints.live_assets + '&p=' + encodeURIComponent( '?page=' + page + '&size=' + size ), callback ); },

            /**
             * Create the specified asset.
             *
             * @param asset The asset to create.
             * @param callback The callback function.
             */
            create: function ( asset, callback ) { ApiService.post( endPoints.live_assets, asset, callback ) },

            /**
             * Delete the specified message.
             *
             * @param message The message to delete.
             * @param callback The callback to call after the operation completes.
             */
            kill: function( asset, callback ) { ApiService.kill( endPoints.live_assets + '&p=' + asset.id, callback ); },

            /**
             * Update the specified message.
             *
             * @param message The message to update.
             * @param callback The callback to call after the operation completes.
             */
            update: function( asset, callback ) { ApiService.update( endPoints.live_assets + '&p=' + asset.id, asset, callback ); }

        }

    } ] )
    .controller( 'LiveAssetController', [ 'LiveAssetService', '$scope', function ( service, $scope ) {

        $scope.data    = [];

        // The current page and the default page size.
        $scope.page        = 0;
        $scope.size        = 10;
        $scope.currentPage = 1;

        /**
         * Refresh the list of messages.
         */
        $scope.refresh = function() { service.list( $scope.page, $scope.size, function ( data ) {
            $scope.data        = data;
            $scope.currentPage = data.number + 1;
        } ); }

        /**
         * Create an asset.
         */
        $scope.create = function( asset ) { service.create( asset, function ( data ) { $scope.refresh(); } ); };

        /**
         * Delete the specified message.
         *
         * @param message
         */
        $scope.kill = function( asset ) { service.kill( asset, function( data ) { $scope.refresh(); } ); };

        /**
         * Save the specified message.
         *
         * @param message
         */
        $scope.update = function( asset ) {
            delete asset.username;
            service.update( asset, function( data ) { $scope.refresh(); } );
        };

        /**
         * Go to the specified page.
         *
         * @param page The page to go to.
         */
        $scope.goToPage = function( page ) {

            // Check that we are in a valid range.
            if ( 0 > page || $scope.data.totalPages <= page ) {
                return;
            }

            $scope.page = page;
            $scope.refresh();
        };

        $scope.refresh();

    } ] );