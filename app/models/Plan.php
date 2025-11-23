<?php
// FILE: /app/models/Plan.php

class Plan extends Model {
    protected $table = 'plans';

    public function getAllActive() {
        return $this->db->fetchAll(
            "SELECT * FROM plans WHERE is_active = 1 ORDER BY price_monthly ASC"
        );
    }

    public function findByName($name) {
        return $this->findBy('name', $name);
    }

    public function getFeatures($planId) {
        $plan = $this->find($planId);
        if ($plan && !empty($plan['features_json'])) {
            return json_decode($plan['features_json'], true);
        }
        return [];
    }
}
