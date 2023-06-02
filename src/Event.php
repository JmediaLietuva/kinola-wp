<?php

namespace Kinola\KinolaWp;

use Kinola\KinolaWp\Api\Event as ApiEvent;

class Event {

    public const FIELD_ID = 'event_id';

    protected ?\WP_Post $post = null;

    public function __construct( \WP_Post $post ) {
        $this->post = $post;
    }

    public function get_post(): ?\WP_Post {
        return $this->post;
    }

    public function get_title() {
        return apply_filters( 'the_title', $this->post->post_title );
    }

    public function get_date(): string {
        $dateTime = Helpers::format_datetime( $this->get_field( 'time' ) );

        return $dateTime->format( get_option( 'date_format' ) );
    }

    public function get_time(): string {
        $dateTime = Helpers::format_datetime( $this->get_field( 'time' ) );

        return $dateTime->format( get_option( 'time_format' ) );
    }

    public function get_poster_url() {
        return $this->get_field( 'production', false )['image']['srcset'];
    }

    public function get_checkout_url(): string {
        return Router::get_event_checkout_url( $this->get_remote_id() );
    }

    public function get_field( string $name, bool $compact = true ) {
        $value = get_post_meta( $this->post->ID, $name, true );

        if ( is_array( $value ) && $compact ) {
            return implode( ', ', $value );
        }

        return $value;
    }

    public function get_local_id(): int {
        return $this->post->ID;
    }

    public function get_remote_id(): string {
        return $this->get_field( self::FIELD_ID );
    }

    public function set_field( string $field, $value ) {
        update_post_meta( $this->post->ID, $field, $value );
    }

    public function set_title( string $production_title, string $date_time ) {
        wp_update_post( [
            'ID'         => $this->get_local_id(),
            'post_title' => self::format_title( $production_title, $date_time ),
        ] );
    }

    public function save_api_data( ApiEvent $event ) {
        foreach ( $event->get_data() as $field => $value ) {
            switch ( $field ) {
                case 'venue':
                    $this->set_venue( $value );
                    break;
                default:
                    $this->set_field( $field, $value );
            }
        }
    }

    public function set_venue( $venue ) {
        wp_insert_term(
            $venue,
            Helpers::get_venue_taxonomy_name()
        );

        $term = get_term_by( 'name', $venue, Helpers::get_venue_taxonomy_name() );

        wp_set_object_terms(
            $this->get_local_id(),
            $term->term_id,
            Helpers::get_venue_taxonomy_name()
        );
    }

    public static function find_by_local_id( int $id ): ?Event {
        $post = get_post( $id );

        if ( $post ) {
            return new Event( $post );
        }

        return null;
    }

    public static function find_by_remote_id( string $id ): ?Event {
        $results = ( new \WP_Query( [
            'post_type'  => Helpers::get_events_post_type(),
            'meta_key'   => self::FIELD_ID,
            'meta_value' => $id,
        ] ) )->get_posts();

        if ( count( $results ) === 1 ) {
            return new Event( $results[0] );
        } else if ( count( $results ) > 1 ) {
            trigger_error( "More than one WP Post matches event ID {$id}", E_USER_WARNING );
        }

        return null;
    }

    public static function create( ApiEvent $api_event ): Event {
        $post = wp_insert_post( [
            'post_title'  => self::format_title( $api_event->get_field( 'production_title' ), $api_event->get_field( 'time' ) ),
            'post_status' => 'publish',
            'post_type'   => Helpers::get_events_post_type(),
        ] );

        $event = new Event( get_post( $post ) );
        $event->save_api_data( $api_event );

        return $event;
    }

    public static function get_upcoming_events( array $args = [], array $meta_query = [] ): array {
        $events = [];
        $params = [
                      'post_type'      => Helpers::get_events_post_type(),
                      'posts_per_page' => - 1,
                      'meta_key'       => 'time',
                      'orderby'        => 'meta_value',
                      'order'          => 'ASC',
                  ] + $args;

        $params['meta_query'] = [
                                    [
                                        'key'     => 'time',
                                        'value'   => gmdate( "Y-m-d\TH:i:s\Z" ),
                                        'compare' => '>=',
                                    ],
                                ] + $meta_query;

        $query = new \WP_Query( $params );

        if ( $query->have_posts() ) {
            foreach ( $query->get_posts() as $post ) {
                $events[] = new Event( $post );
            }
        }

        return $events;
    }

    public static function format_title( string $production_title, string $date_time ): string {
        $dateTime = Helpers::format_datetime( $date_time );

        return $production_title . ' - ' .
               $dateTime->format( get_option( 'date_format' ) ) . ' ' .
               $dateTime->format( get_option( 'time_format' ) );
    }
}
